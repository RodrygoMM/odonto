<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Models\CnpjRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Exibe a view de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Processa o registro de um novo cliente (Tenant + Matriz + User ADM + CNPJ da Matriz),
     * com validação de CPF/CNPJ e situação cadastral ATIVA na Receita.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1) Validação básica de formulário e unicidade
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                'max:14',
                'unique:users,cpf',
            ],
            'cnpj' => [
                'required',
                'string',
                'max:18',
                // CNPJ não pode repetir em outra rede (cnpj_matriz)
                'unique:tenants,cnpj_matriz',
                // nem em outra unidade (matriz ou filial)
                'unique:units,cnpj',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2) Normaliza e valida CPF/CNPJ (dígitos verificadores)
        $cpfNumerico  = $this->onlyDigits($validated['cpf']);
        $cnpjNumerico = $this->onlyDigits($validated['cnpj']);

        if (! $this->isValidCpf($cpfNumerico)) {
            return back()
                ->withErrors(['cpf' => 'CPF inválido. Verifique os números digitados.'])
                ->withInput();
        }

        if (! $this->isValidCnpj($cnpjNumerico)) {
            return back()
                ->withErrors(['cnpj' => 'CNPJ inválido. Verifique os números digitados.'])
                ->withInput();
        }

        // 3) Consulta Receita via BrasilAPI e exige situação cadastral ATIVA
        try {
            $response = Http::get("https://brasilapi.com.br/api/cnpj/v1/{$cnpjNumerico}");
        } catch (\Throwable $e) {
            return back()
                ->withErrors([
                    'cnpj' => 'Não foi possível validar o CNPJ na Receita Federal. Tente novamente em alguns minutos.',
                ])
                ->withInput();
        }

        if (! $response->successful()) {
            return back()
                ->withErrors([
                    'cnpj' => 'CNPJ não encontrado na Receita Federal. Verifique o número informado.',
                ])
                ->withInput();
        }

        $cnpjData = $response->json();

        $situacaoDesc = $cnpjData['descricao_situacao_cadastral'] ?? null;
        $situacaoDescUpper = $situacaoDesc ? mb_strtoupper($situacaoDesc, 'UTF-8') : null;

        if ($situacaoDescUpper !== 'ATIVA') {
            $situacaoTexto = $situacaoDesc ?: 'desconhecida';
            return back()
                ->withErrors([
                    'cnpj' => "Somente CNPJs com situação cadastral ATIVA podem se cadastrar. Situação atual na Receita: {$situacaoTexto}.",
                ])
                ->withInput();
        }

        // 4) Cria Tenant, Matriz, User e espelho fiscal em transação
        $user = null;

        DB::transaction(function () use (&$user, $validated, $cnpjNumerico, $cnpjData) {
            // 4.1) Criar o Tenant (rede / cliente)
            $tenant = Tenant::create([
                'nome_fantasia'  => $validated['name'],
                'razao_social'   => $cnpjData['razao_social'] ?? null,
                'cnpj_matriz'    => $validated['cnpj'],
                'email_billing'  => $validated['email'],
                'licenses_total' => 1,   // começa com 1 licença contratada
                'licenses_used'  => 0,
                'ativo'          => true,
            ]);

            // 4.2) Criar a unidade matriz
            $unit = Unit::create([
                'tenant_id' => $tenant->id,
                'nome'      => 'Matriz',
                'cnpj'      => $validated['cnpj'],
                'is_matriz' => true,
                'ativo'     => true,
            ]);

            // 4.3) Criar o usuário ADM
            $user = User::create([
                'tenant_id' => $tenant->id,
                'unit_id'   => $unit->id,
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'cpf'       => $validated['cpf'],
                'password'  => Hash::make($validated['password']),
            ]);

            // 4.4) Salvar espelho fiscal do CNPJ da matriz
            CnpjRegistration::create([
                'tenant_id' => $tenant->id,
                'unit_id'   => $unit->id,
                'cnpj'      => $cnpjData['cnpj'] ?? $validated['cnpj'],
                'razao_social' => $cnpjData['razao_social'] ?? null,
                'nome_fantasia' => $cnpjData['nome_fantasia'] ?? null,
                'tipo_estabelecimento' => $cnpjData['descricao_matriz_filial'] ?? 'MATRIZ',
                'data_abertura' => $cnpjData['data_inicio_atividade'] ?? null,
                'porte' => $cnpjData['descricao_porte'] ?? null,
                'cnae_principal_codigo' => isset($cnpjData['cnae_fiscal']) ? (string) $cnpjData['cnae_fiscal'] : null,
                'cnae_principal_descricao' => $cnpjData['cnae_fiscal_descricao'] ?? null,
                'cnaes_secundarios' => $cnpjData['cnaes_secundarias'] ?? null,
                'natureza_juridica_codigo' => $cnpjData['codigo_natureza_juridica'] ?? null,
                'natureza_juridica_descricao' => $cnpjData['natureza_juridica'] ?? null,
                'logradouro' => $cnpjData['logradouro'] ?? null,
                'numero' => $cnpjData['numero'] ?? null,
                'complemento' => $cnpjData['complemento'] ?? null,
                'bairro' => $cnpjData['bairro'] ?? null,
                'municipio' => $cnpjData['municipio'] ?? null,
                'uf' => $cnpjData['uf'] ?? null,
                'cep' => $cnpjData['cep'] ?? null,
                'email' => $cnpjData['email'] ?? null,
                'telefone' => $cnpjData['ddd_telefone_1'] ?? null,
                'situacao_cadastral' => $cnpjData['descricao_situacao_cadastral'] ?? null,
                'data_situacao_cadastral' => $cnpjData['data_situacao_cadastral'] ?? null,
                'motivo_situacao_cadastral' => $cnpjData['motivo_situacao_cadastral'] ?? null,
                'situacao_especial' => $cnpjData['situacao_especial'] ?? null,
                'data_situacao_especial' => $cnpjData['data_situicao_especial'] ?? ($cnpjData['data_situacao_especial'] ?? null),
                'raw_payload' => $cnpjData,
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Deixa só os dígitos numéricos.
     */
    private function onlyDigits(string $value): string
    {
        return preg_replace('/\D/', '', $value);
    }

    /**
     * Validação de CPF (dígitos verificadores).
     */
    private function isValidCpf(string $cpf): bool
    {
        // 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Rejeita sequências iguais (111.111.111-11, etc.)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Calcula dígito 1
        $sum = 0;
        for ($i = 0, $weight = 10; $i < 9; $i++, $weight--) {
            $sum += (int) $cpf[$i] * $weight;
        }

        $rest = $sum % 11;
        $digit1 = ($rest < 2) ? 0 : 11 - $rest;

        if ((int) $cpf[9] !== $digit1) {
            return false;
        }

        // Calcula dígito 2
        $sum = 0;
        for ($i = 0, $weight = 11; $i < 10; $i++, $weight--) {
            $sum += (int) $cpf[$i] * $weight;
        }

        $rest = $sum % 11;
        $digit2 = ($rest < 2) ? 0 : 11 - $rest;

        return (int) $cpf[10] === $digit2;
    }

    /**
     * Validação de CNPJ (dígitos verificadores).
     */
    private function isValidCnpj(string $cnpj): bool
    {
        // 14 dígitos
        if (strlen($cnpj) !== 14) {
            return false;
        }

        // Rejeita sequências iguais
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $numbers = array_map('intval', str_split($cnpj));

        // Dígito 1
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $numbers[$i] * $weights1[$i];
        }
        $rest = $sum % 11;
        $digit1 = ($rest < 2) ? 0 : 11 - $rest;

        if ($numbers[12] !== $digit1) {
            return false;
        }

        // Dígito 2
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $numbers[$i] * $weights2[$i];
        }
        $rest = $sum % 11;
        $digit2 = ($rest < 2) ? 0 : 11 - $rest;

        return $numbers[13] === $digit2;
    }
}

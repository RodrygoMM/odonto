<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Carbon\Carbon;

class InviteUserController extends Controller
{
    /**
     * Formulário de convite / pré-cadastro de novo usuário.
     */
    public function create(): View
    {
        $authUser = Auth::user();
        $tenant   = $authUser?->tenant;

        // Lista de unidades para a MATRIZ escolher (na view a FILIAL ignora)
        $units = $tenant
            ? $tenant->units()->orderByDesc('is_matriz')->orderBy('nome')->get()
            : collect();

        return view('users.invite', [
            'tenant' => $tenant,
            'units'  => $units,
        ]);
    }

    /**
     * Processa o pré-cadastro / convite de um novo usuário.
     *
     * Regras:
     * - Matriz: pode escolher qualquer unidade do tenant.
     * - Filial: sempre vincula o usuário à sua própria unidade (ignora unit_id do request).
     */
    public function store(Request $request): RedirectResponse
    {
        $authUser = Auth::user();

        if (! $authUser) {
            abort(403, 'Usuário não autenticado.');
        }

        $tenant      = $authUser->tenant;
        $currentUnit = $authUser->unit;

        if (! $tenant || ! $currentUnit) {
            abort(403, 'Contexto de tenant/unidade não encontrado.');
        }

        $isMatriz = (bool) $currentUnit->is_matriz;

        // Regras de validação base
        $rules = [
            'name' => ['required', 'string', 'max:255'],

            'cpf' => [
                'required',
                'string',
                'max:14',
            ],

            'data_nascimento' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
            ],
        ];

        // Matriz pode escolher unit_id; filial ignora.
        if ($isMatriz) {
            $rules['unit_id'] = ['required', 'integer', 'exists:units,id'];
        }

        $validated = $request->validate($rules);

        // Normaliza CPF (só dígitos)
        $cpfNumerico = $this->onlyDigits($validated['cpf']);

        // Validação de CPF (dígitos verificadores)
        if (! $this->isValidCpf($cpfNumerico)) {
            return back()
                ->withErrors(['cpf' => 'CPF inválido. Verifique os números digitados.'])
                ->withInput();
        }

        // Garante unicidade do CPF já normalizado
        if (User::where('cpf', $cpfNumerico)->exists()) {
            return back()
                ->withErrors(['cpf' => 'Já existe um usuário cadastrado com este CPF.'])
                ->withInput();
        }

        // Verifica maioridade (>= 18 anos)
        $dataNascimento = Carbon::parse($validated['data_nascimento']);
        if ($dataNascimento->age < 18) {
            return back()
                ->withErrors(['data_nascimento' => 'O usuário precisa ter pelo menos 18 anos.'])
                ->withInput();
        }

        // Resolve a unidade de vínculo
        if ($isMatriz) {
            // Matriz: usa unit_id escolhido, mas garante que pertence ao mesmo tenant
            $unit = Unit::where('id', $validated['unit_id'])
                ->where('tenant_id', $tenant->id)
                ->first();

            if (! $unit) {
                return back()
                    ->withErrors(['unit_id' => 'Unidade inválida para este cliente.'])
                    ->withInput();
            }
        } else {
            // Filial: sempre usa a própria unidade, ignora qualquer unit_id do request
            $unit = $currentUnit;
        }

        // Gera uma senha aleatória (o usuário vai trocar ao clicar no link do e-mail)
        $senhaAleatoria = Str::random(32);

        // Cria o usuário
        $user = User::create([
            'tenant_id'       => $tenant->id,
            'unit_id'         => $unit->id,
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'cpf'             => $cpfNumerico,
            'data_nascimento' => $dataNascimento->format('Y-m-d'),
            'ativo'           => true,
            'monetizado'      => false,
            'password'        => Hash::make($senhaAleatoria),
        ]);

        /**
         * ENVIO DO E-MAIL DE CONVITE
         *
         * Aqui usamos o fluxo padrão do Laravel de "Esqueci minha senha".
         * O convidado recebe um link para definir a senha dele.
         * As views do Breeze (reset-password / forgot-password) já cuidam disso.
         */
        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            // Não falhou a criação do usuário, só o envio do e-mail.
            // Podemos avisar quem convidou.
            return redirect()
                ->route('dashboard')
                ->with('status', 'Usuário criado, mas houve um problema ao enviar o e-mail de convite: ' . __($status));
        }

        return redirect()
            ->route('dashboard')
            ->with('status', 'Usuário convidado com sucesso. O convite foi enviado para ' . $user->email . '.');
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

        $rest   = $sum % 11;
        $digit1 = ($rest < 2) ? 0 : 11 - $rest;

        if ((int) $cpf[9] !== $digit1) {
            return false;
        }

        // Calcula dígito 2
        $sum = 0;
        for ($i = 0, $weight = 11; $i < 10; $i++, $weight--) {
            $sum += (int) $cpf[$i] * $weight;
        }

        $rest   = $sum % 11;
        $digit2 = ($rest < 2) ? 0 : 11 - $rest;

        return (int) $cpf[10] === $digit2;
    }
}

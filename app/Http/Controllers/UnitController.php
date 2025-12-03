<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\CnpjRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * Lista todas as unidades (matriz + filiais) do tenant atual.
     */
    public function index(Request $request): View
    {
        $tenantId = $request->user()->tenant_id;

        $units = Unit::where('tenant_id', $tenantId)
            ->orderByDesc('is_matriz')
            ->orderBy('nome')
            ->get();

        return view('units.index', compact('units'));
    }

    /**
     * Formulário para cadastrar uma nova filial.
     */
    public function create(Request $request): View
    {
        return view('units.create');
    }

    /**
     * Salva uma nova filial para o tenant atual.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenantId = $request->user()->tenant_id;

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cnpj' => [
                'required',
                'string',
                'max:18',
                'unique:units,cnpj',
            ],
        ]);

        // Normaliza o CNPJ (só números) para consulta em API
        $cnpjNumerico = preg_replace('/\D/', '', $validated['cnpj']);

        // 1) Cria a unidade (filial)
        $unit = Unit::create([
            'tenant_id' => $tenantId,
            'nome'      => $validated['nome'],
            'cnpj'      => $validated['cnpj'], // pode guardar formatado
            'is_matriz' => false,
            'ativo'     => true,
        ]);

        // 2) Tenta buscar dados do CNPJ em uma API pública (ex.: BrasilAPI)
        try {
            $response = Http::get("https://brasilapi.com.br/api/cnpj/v1/{$cnpjNumerico}");

            if ($response->successful()) {
                $data = $response->json();

                CnpjRegistration::create([
                    'tenant_id' => $tenantId,
                    'unit_id'   => $unit->id,
                    'cnpj'      => $data['cnpj'] ?? $validated['cnpj'],
                    'razao_social' => $data['razao_social'] ?? null,
                    'nome_fantasia' => $data['nome_fantasia'] ?? null,
                    'tipo_estabelecimento' => $data['descricao_matriz_filial'] ?? null,
                    'data_abertura' => $data['data_inicio_atividade'] ?? null,
                    'porte' => $data['descricao_porte'] ?? null,
                    'cnae_principal_codigo' => isset($data['cnae_fiscal']) ? (string) $data['cnae_fiscal'] : null,
                    'cnae_principal_descricao' => $data['cnae_fiscal_descricao'] ?? null,
                    'cnaes_secundarios' => $data['cnaes_secundarias'] ?? null,
                    'natureza_juridica_codigo' => $data['codigo_natureza_juridica'] ?? null,
                    'natureza_juridica_descricao' => $data['natureza_juridica'] ?? null,
                    'logradouro' => $data['logradouro'] ?? null,
                    'numero' => $data['numero'] ?? null,
                    'complemento' => $data['complemento'] ?? null,
                    'bairro' => $data['bairro'] ?? null,
                    'municipio' => $data['municipio'] ?? null,
                    'uf' => $data['uf'] ?? null,
                    'cep' => $data['cep'] ?? null,
                    'email' => $data['email'] ?? null,
                    'telefone' => $data['ddd_telefone_1'] ?? null,
                    'situacao_cadastral' => $data['descricao_situacao_cadastral'] ?? null,
                    'data_situacao_cadastral' => $data['data_situacao_cadastral'] ?? null,
                    'motivo_situacao_cadastral' => $data['motivo_situacao_cadastral'] ?? null,
                    'situacao_especial' => $data['situacao_especial'] ?? null,
                    'data_situacao_especial' => $data['data_situicao_especial'] ?? ($data['data_situacao_especial'] ?? null),
                    'raw_payload' => $data,
                ]);
            }
        } catch (\Throwable $e) {
            // logger()->warning('Falha ao consultar CNPJ', ['cnpj' => $cnpjNumerico, 'error' => $e->getMessage()]);
        }

        return redirect()
            ->route('units.index')
            ->with('status', 'Unidade filial cadastrada com sucesso.');
    }

    /**
     * Mostra detalhes da unidade + espelho do CNPJ.
     */
    public function show(Request $request, Unit $unit): View
    {
        $this->authorizeUnit($request, $unit);

        $unit->load('tenant', 'cnpjRegistration');

        return view('units.show', compact('unit'));
    }

    /**
     * Formulário de edição:
     * apenas E-mail, Telefone e Situação Cadastral do registro fiscal.
     * (Matriz e Filial)
     */
    public function edit(Request $request, Unit $unit): View
    {
        $this->authorizeUnit($request, $unit);

        $unit->load('cnpjRegistration');

        return view('units.edit', compact('unit'));
    }

    /**
     * Atualiza apenas E-mail, Telefone e Situação Cadastral
     * do registro fiscal (CnpjRegistration).
     */
    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $this->authorizeUnit($request, $unit);

        $validated = $request->validate([
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:50'],
            'situacao_cadastral' => ['nullable', 'string', 'max:255'],
        ]);

        // Garante que exista um registro fiscal; se não houver, cria um "casco"
        $cnpjReg = $unit->cnpjRegistration;

        if (! $cnpjReg) {
            $cnpjReg = CnpjRegistration::create([
                'tenant_id' => $unit->tenant_id,
                'unit_id'   => $unit->id,
                'cnpj'      => $unit->cnpj,
            ]);
        }

        $cnpjReg->update([
            'email'              => $validated['email'] ?? null,
            'telefone'           => $validated['telefone'] ?? null,
            'situacao_cadastral' => $validated['situacao_cadastral'] ?? null,
        ]);

        return redirect()
            ->route('units.show', $unit)
            ->with('status', 'Dados fiscais atualizados com sucesso.');
    }

    /**
     * Remove uma unidade (apenas filiais) e seu CNPJ fiscal.
     * Bloqueado em produção.
     */
    public function destroy(Request $request, Unit $unit): RedirectResponse
    {
        $this->authorizeUnit($request, $unit);

        // Nunca permitir em produção
        if (app()->environment('production')) {
            abort(403, 'Exclusão de unidades não é permitida em produção.');
        }

        // Nunca permitir apagar a matriz
        if ($unit->is_matriz) {
            abort(403, 'A unidade matriz não pode ser excluída.');
        }

        $unit->delete(); // cnpj_registrations com unit_id vão junto (cascade)

        return redirect()
            ->route('units.index')
            ->with('status', 'Unidade (CNPJ) removida com sucesso.');
    }

    /**
     * Garante que a unidade pertence ao tenant do usuário logado.
     */
    protected function authorizeUnit(Request $request, Unit $unit): void
    {
        if ($unit->tenant_id !== $request->user()->tenant_id) {
            abort(403);
        }
    }
}

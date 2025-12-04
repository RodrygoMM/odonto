<?php

namespace App\Http\Controllers;

use App\Models\PrecificacaoLicenca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrecificacaoLicencaController extends Controller
{
    /**
     * Garante que somente o CPF autorizado acesse.
     */
    protected function ensureMasterPricingUser(): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Acesso não autorizado.');
        }

        // normaliza CPF: remove tudo que não for número
        $userCpf = $user->cpf ?? null;

        if ($userCpf) {
            $userCpf = preg_replace('/\D/', '', $userCpf);
        }

        if ($userCpf !== '05628981907') {
            abort(403, 'Acesso não autorizado.');
        }
    }

    /**
     * Lista todas as versões de precificação ATIVAS.
     */
    public function index()
    {
        $this->ensureMasterPricingUser();

        $precificacoes = PrecificacaoLicenca::where('ativa', true)
            ->orderByDesc('competencia_inicio')
            ->get();

        return view('precificacao_licencas.index', compact('precificacoes'));
    }

    /**
     * Formulário de criação de nova versão de precificação.
     */
    public function create()
    {
        $this->ensureMasterPricingUser();

        $precificacao = new PrecificacaoLicenca([
            'ativa' => true,
            'moeda' => 'BRL',
        ]);

        return view('precificacao_licencas.create', compact('precificacao'));
    }

    /**
     * Salva nova versão de precificação (registro original).
     */
    public function store(Request $request)
    {
        $this->ensureMasterPricingUser();

        $dados = $this->validateData($request);

        $dados['moeda'] = $dados['moeda'] ?? 'BRL';
        $dados['criado_por_usuario_id'] = Auth::id();
        $dados['atualizado_por_usuario_id'] = Auth::id();

        // Registro original: id_origem permanece null (raiz do conjunto)
        $dados['id_origem'] = null;

        PrecificacaoLicenca::create($dados);

        return redirect()
            ->route('precificacao-licencas.index')
            ->with('success', 'Versão de precificação criada com sucesso.');
    }

    /**
     * Formulário de edição de uma versão existente.
     */
    public function edit(PrecificacaoLicenca $precificacao_licenca)
    {
        $this->ensureMasterPricingUser();

        $precificacao = $precificacao_licenca;

        return view('precificacao_licencas.edit', compact('precificacao'));
    }

    /**
     * Atualiza uma versão de precificação.
     *
     * Regra:
     *  - NÃO sobrescrever registro antigo;
     *  - inativar o antigo;
     *  - criar NOVA linha com id_origem apontando para o registro original
     *    (ou seja, para a "raiz" do conjunto).
     *  - Competência fim digitada no formulário passa a valer para a NOVA versão.
     */
    public function update(Request $request, PrecificacaoLicenca $precificacao_licenca)
    {
        $this->ensureMasterPricingUser();

        $dados = $this->validateData($request, $precificacao_licenca->id);
        $userId = Auth::id();

        DB::transaction(function () use ($precificacao_licenca, $dados, $userId) {
            // Descobre a "origem" do conjunto:
            // - se o registro já tem id_origem, usamos ele;
            // - se não tem, o próprio id atual vira a origem.
            $origemId = $precificacao_licenca->id_origem ?? $precificacao_licenca->id;

            // 1) Inativa a versão antiga, sem perder histórico
            $precificacao_licenca->update([
                'ativa'                     => false,
                // aqui NÃO alteramos competencia_fim da antiga:
                // ela mantém o valor que já tinha (pode ser null)
                'atualizado_por_usuario_id' => $userId,
            ]);

            // 2) Cria uma NOVA versão com os dados informados no form
            $nova = $dados;

            // Nova versão sempre nasce ativa
            $nova['ativa'] = true;

            // A nova versão é ligada à origem do conjunto
            $nova['id_origem'] = $origemId;

            // Competência fim da NOVA versão vem do form (pode ser null)
            $nova['competencia_fim'] = $dados['competencia_fim'] ?? null;

            $nova['moeda'] = $nova['moeda'] ?? 'BRL';
            $nova['criado_por_usuario_id'] = $userId;
            $nova['atualizado_por_usuario_id'] = $userId;

            PrecificacaoLicenca::create($nova);
        });

        return redirect()
            ->route('precificacao-licencas.index')
            ->with('success', 'Nova versão de precificação criada. A anterior foi inativada e mantida para histórico.');
    }

    /**
     * Validação centralizada dos dados do formulário.
     */
    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'descricao' => ['required', 'string', 'max:150'],
            'ativa' => ['nullable', 'boolean'],
            'moeda' => ['nullable', 'string', 'size:3'],
            'valor_combo_inicial_mensal' => ['required', 'numeric', 'min:0'],
            'valor_licenca_medico_adicional_mensal' => ['required', 'numeric', 'min:0'],
            'valor_licenca_recepcionista_adicional_mensal' => ['required', 'numeric', 'min:0'],
            'competencia_inicio' => ['required', 'date'],
            'competencia_fim' => ['nullable', 'date', 'after_or_equal:competencia_inicio'],
            'data_inicio_comunicacao_reajuste' => ['nullable', 'date'],
            'observacao_interna' => ['nullable', 'string'],
            'motivo_alteracao' => ['nullable', 'string', 'max:255'],
        ]);
    }
}

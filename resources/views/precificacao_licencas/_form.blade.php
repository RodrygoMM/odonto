<div class="flex flex-col gap-4">

    <div>
        <label for="descricao" class="block text-sm font-medium text-gray-700">
            Descrição *
        </label>
        <input
            type="text"
            id="descricao"
            name="descricao"
            value="{{ old('descricao', $precificacao->descricao) }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </div>

    <div class="flex items-center">
        <input
            type="checkbox"
            id="ativa"
            name="ativa"
            value="1"
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
            {{ old('ativa', $precificacao->ativa) ? 'checked' : '' }}
        >
        <label for="ativa" class="ms-2 block text-sm text-gray-700">
            Versão ativa
        </label>
    </div>

    <div>
        <label for="moeda" class="block text-sm font-medium text-gray-700">
            Moeda
        </label>
        <input
            type="text"
            id="moeda"
            name="moeda"
            value="{{ old('moeda', $precificacao->moeda ?? 'BRL') }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </div>

    <div>
        <label for="valor_combo_inicial_mensal" class="block text-sm font-medium text-gray-700">
            Valor combo inicial (mensal) *
        </label>
        <input
            type="number"
            step="0.01"
            min="0"
            id="valor_combo_inicial_mensal"
            name="valor_combo_inicial_mensal"
            value="{{ old('valor_combo_inicial_mensal', $precificacao->valor_combo_inicial_mensal) }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </div>

    <div>
        <label for="valor_licenca_medico_adicional_mensal" class="block text-sm font-medium text-gray-700">
            Valor médico adicional (mensal) *
        </label>
        <input
            type="number"
            step="0.01"
            min="0"
            id="valor_licenca_medico_adicional_mensal"
            name="valor_licenca_medico_adicional_mensal"
            value="{{ old('valor_licenca_medico_adicional_mensal', $precificacao->valor_licenca_medico_adicional_mensal) }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </div>

    <div>
        <label for="valor_licenca_recepcionista_adicional_mensal" class="block text-sm font-medium text-gray-700">
            Valor recepcionista adicional (mensal) *
        </label>
        <input
            type="number"
            step="0.01"
            min="0"
            id="valor_licenca_recepcionista_adicional_mensal"
            name="valor_licenca_recepcionista_adicional_mensal"
            value="{{ old('valor_licenca_recepcionista_adicional_mensal', $precificacao->valor_licenca_recepcionista_adicional_mensal) }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </div>

    <div>
        <label for="competencia_inicio" class="block text-sm font-medium text-gray-700">
            Competência início *
        </label>
        <input
            type="date"
            id="competencia_inicio"
            name="competencia_inicio"
            value="{{ old('competencia_inicio', optional($precificacao->competencia_inicio)->format('Y-m-d')) }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </div>

    <div>
        <label for="competencia_fim" class="block text-sm font-medium text-gray-700">
            Competência fim
        </label>
        <input
            type="date"
            id="competencia_fim"
            name="competencia_fim"
            value="{{ old('competencia_fim', optional($precificacao->competencia_fim)->format('Y-m-d')) }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </div>

    <div>
        <label for="data_inicio_comunicacao_reajuste" class="block text-sm font-medium text-gray-700">
            Data início comunicação do reajuste
        </label>
        <input
            type="date"
            id="data_inicio_comunicacao_reajuste"
            name="data_inicio_comunicacao_reajuste"
            value="{{ old('data_inicio_comunicacao_reajuste', optional($precificacao->data_inicio_comunicacao_reajuste)->format('Y-m-d')) }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </div>

    <div>
        <label for="motivo_alteracao" class="block text-sm font-medium text-gray-700">
            Motivo da alteração
        </label>
        <input
            type="text"
            id="motivo_alteracao"
            name="motivo_alteracao"
            value="{{ old('motivo_alteracao', $precificacao->motivo_alteracao) }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </div>

    <div>
        <label for="observacao_interna" class="block text-sm font-medium text-gray-700">
            Observações internas
        </label>
        <textarea
            id="observacao_interna"
            name="observacao_interna"
            rows="4"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >{{ old('observacao_interna', $precificacao->observacao_interna) }}</textarea>
    </div>

    <div class="pt-2">
        <button
            type="submit"
            class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            {{ $botaoTexto ?? 'Salvar' }}
        </button>

        <a href="{{ route('precificacao-licencas.index') }}"
           class="inline-flex items-center px-4 py-2 ms-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Voltar
        </a>
    </div>
</div>

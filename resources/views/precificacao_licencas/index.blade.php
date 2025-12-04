<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h1 class="text-xl font-semibold mb-4">
                        Tabela de Precificação de Licenças
                    </h1>

                    @if (session('success'))
                        <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <a href="{{ route('precificacao-licencas.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            + Nova versão de precificação
                        </a>
                    </div>

                    @if ($precificacoes->isEmpty())
                        <p class="text-sm text-gray-600">
                            Não há versões de precificação cadastradas.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 border-b">ID</th>
                                        <th class="px-3 py-2 border-b">Descrição</th>
                                        <th class="px-3 py-2 border-b">Ativa</th>
                                        <th class="px-3 py-2 border-b">Moeda</th>
                                        <th class="px-3 py-2 border-b">Combo inicial (mensal)</th>
                                        <th class="px-3 py-2 border-b">Médico adicional (mensal)</th>
                                        <th class="px-3 py-2 border-b">Recepcionista adicional (mensal)</th>
                                        <th class="px-3 py-2 border-b">Competência início</th>
                                        <th class="px-3 py-2 border-b">Competência fim</th>
                                        <th class="px-3 py-2 border-b">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($precificacoes as $precificacao)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 border-b">{{ $precificacao->id }}</td>
                                            <td class="px-3 py-2 border-b">{{ $precificacao->descricao }}</td>
                                            <td class="px-3 py-2 border-b">
                                                {{ $precificacao->ativa ? 'Sim' : 'Não' }}
                                            </td>
                                            <td class="px-3 py-2 border-b">{{ $precificacao->moeda }}</td>
                                            <td class="px-3 py-2 border-b">
                                                R$ {{ number_format($precificacao->valor_combo_inicial_mensal, 2, ',', '.') }}
                                            </td>
                                            <td class="px-3 py-2 border-b">
                                                R$ {{ number_format($precificacao->valor_licenca_medico_adicional_mensal, 2, ',', '.') }}
                                            </td>
                                            <td class="px-3 py-2 border-b">
                                                R$ {{ number_format($precificacao->valor_licenca_recepcionista_adicional_mensal, 2, ',', '.') }}
                                            </td>
                                            <td class="px-3 py-2 border-b">
                                                {{ optional($precificacao->competencia_inicio)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-3 py-2 border-b">
                                                @if ($precificacao->competencia_fim)
                                                    {{ $precificacao->competencia_fim->format('d/m/Y') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 border-b">
                                                <a href="{{ route('precificacao-licencas.edit', $precificacao) }}"
                                                   class="inline-flex items-center px-3 py-1 bg-yellow-400 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                    Editar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rede do Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">
                            Unidades (Matriz e Filiais)
                        </h3>

                        <a
                            href="{{ route('units.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Nova Filial
                        </a>
                    </div>

                    @if ($units->isEmpty())
                        <p class="text-sm text-gray-600">
                            Nenhuma unidade cadastrada além da matriz.
                        </p>
                    @else
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-4 py-2 text-left">Nome</th>
                                    <th class="px-4 py-2 text-left">CNPJ</th>
                                    <th class="px-4 py-2 text-left">Tipo</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($units as $unit)
                                    <tr class="border-b">
                                        <td class="px-4 py-2">
                                            {{ $unit->nome }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $unit->cnpj }}
                                        </td>
                                        <td class="px-4 py-2">
                                            @if ($unit->is_matriz)
                                                <span class="text-xs font-semibold text-indigo-700">
                                                    Matriz
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-700">
                                                    Filial
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if ($unit->ativo)
                                                <span class="text-xs text-green-700">
                                                    Ativa
                                                </span>
                                            @else
                                                <span class="text-xs text-red-700">
                                                    Inativa
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 space-x-2">
                                            <a
                                                href="{{ route('units.show', $unit) }}"
                                                class="inline-flex items-center px-3 py-1 border border-indigo-600 rounded-md text-xs font-semibold text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                            >
                                                Ver detalhes
                                            </a>

                                            {{-- Botão de excluir apenas em ambiente NÃO produção e somente para filiais --}}
                                            @if (! $unit->is_matriz && ! app()->environment('production'))
                                                <form
                                                    method="POST"
                                                    action="{{ route('units.destroy', $unit) }}"
                                                    class="inline"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir esta unidade (CNPJ) do banco?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center px-3 py-1 border border-red-600 rounded-md text-xs font-semibold text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                    >
                                                        Excluir
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

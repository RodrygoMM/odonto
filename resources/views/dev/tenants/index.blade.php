<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Painel DEV - Tenants</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-lg font-semibold text-gray-800">
                        Painel DEV — CNPJs Matriz Cadastrados
                    </h1>

                    <span class="text-xs text-gray-500">
                        Ambiente: {{ app()->environment() }}
                    </span>
                </div>
            </header>

            <main class="flex-1 py-8">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @if (session('status'))
                        <div class="mb-4 text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <p class="text-sm text-gray-700">
                            Este painel serve <span class="font-semibold">apenas para desenvolvimento</span>.
                            Aqui você pode excluir completamente um tenant (CNPJ matriz), incluindo:
                            unidades, usuários e registros fiscais (CNPJ).
                        </p>
                        <p class="mt-1 text-xs text-red-600">
                            Não há necessidade de login para usar este painel, mas ele é bloqueado em produção.
                        </p>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            @if ($tenants->isEmpty())
                                <p class="text-sm text-gray-600">
                                    Nenhum tenant cadastrado.
                                </p>
                            @else
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="px-4 py-2 text-left">ID</th>
                                            <th class="px-4 py-2 text-left">Nome Fantasia</th>
                                            <th class="px-4 py-2 text-left">CNPJ Matriz</th>
                                            <th class="px-4 py-2 text-left">Usuário (primeiro cadastro)</th>
                                            <th class="px-4 py-2 text-left">Criado em</th>
                                            <th class="px-4 py-2 text-left">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tenants as $tenant)
                                            @php
                                                $adminUser = \App\Models\User::where('tenant_id', $tenant->id)
                                                    ->orderBy('id')
                                                    ->first();
                                            @endphp

                                            <tr class="border-b">
                                                <td class="px-4 py-2">
                                                    {{ $tenant->id }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    {{ $tenant->nome_fantasia ?? '—' }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    {{ $tenant->cnpj_matriz ?? '—' }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    @if ($adminUser)
                                                        <div class="flex flex-col">
                                                            <span class="font-semibold text-gray-800">
                                                                {{ $adminUser->name }}
                                                            </span>
                                                            <span class="text-xs text-gray-600">
                                                                {{ $adminUser->email }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-500">Nenhum usuário encontrado</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2">
                                                    {{ optional($tenant->created_at)->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    <div x-data="{ open: false }" class="inline-block">
                                                        <button
                                                            type="button"
                                                            @click="open = true"
                                                            class="inline-flex items-center px-3 py-1 border border-red-600 rounded-md text-xs font-semibold text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                        >
                                                            Excluir tudo
                                                        </button>

                                                        <!-- Modal -->
                                                        <div
                                                            x-show="open"
                                                            x-cloak
                                                            class="fixed inset-0 flex items-center justify-center z-50"
                                                        >
                                                            <!-- backdrop -->
                                                            <div
                                                                class="fixed inset-0 bg-gray-800 bg-opacity-50"
                                                                @click="open = false"
                                                            ></div>

                                                            <!-- modal content -->
                                                            <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 z-10">
                                                                <div class="px-6 py-4 border-b">
                                                                    <h3 class="text-lg font-semibold text-gray-800">
                                                                        Confirmar exclusão total
                                                                    </h3>
                                                                </div>

                                                                <div class="px-6 py-4 text-sm text-gray-700">
                                                                    <p class="mb-2">
                                                                        Tem certeza que deseja excluir este
                                                                        <span class="font-semibold">tenant (CNPJ matriz)</span>
                                                                        e <span class="font-semibold">tudo ligado a ele</span>?
                                                                    </p>
                                                                    <p class="mb-2">
                                                                        <span class="font-semibold">Nome Fantasia:</span>
                                                                        {{ $tenant->nome_fantasia ?? '—' }}
                                                                    </p>
                                                                    <p class="mb-2">
                                                                        <span class="font-semibold">CNPJ:</span>
                                                                        {{ $tenant->cnpj_matriz ?? '—' }}
                                                                    </p>
                                                                    @if ($adminUser)
                                                                        <p class="mb-2">
                                                                            <span class="font-semibold">Usuário:</span>
                                                                            {{ $adminUser->name }} ({{ $adminUser->email }})
                                                                        </p>
                                                                    @endif
                                                                    <p class="mt-3 text-xs text-red-600">
                                                                        Esta ação irá apagar:
                                                                        tenants, unidades, usuários e registros fiscais (CNPJ).
                                                                        Não pode ser desfeita.
                                                                    </p>
                                                                </div>

                                                                <div class="px-6 py-4 border-t flex justify-end space-x-3">
                                                                    <button
                                                                        type="button"
                                                                        @click="open = false"
                                                                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                                                                    >
                                                                        Cancelar
                                                                    </button>

                                                                    <form
                                                                        method="POST"
                                                                        action="{{ route('dev.tenants.destroy', $tenant) }}"
                                                                    >
                                                                        @csrf
                                                                        @method('DELETE')

                                                                        <button
                                                                            type="submit"
                                                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                                        >
                                                                            Excluir definitivamente
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- /Modal -->
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a
                            href="{{ url('/') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 underline"
                        >
                            Voltar para a tela inicial
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>

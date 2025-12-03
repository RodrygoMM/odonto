<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Odonto - Bem-vindo</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-100">
        <div class="relative min-h-screen flex flex-col justify-center items-center">
            <div class="max-w-2xl mx-auto px-4">
                <h1 class="text-3xl font-bold text-gray-900 text-center mb-4">
                    Odonto &mdash; Plataforma Multi-tenant
                </h1>

                <p class="text-sm text-gray-700 text-center mb-8">
                    Bem-vindo à sua base de desenvolvimento. Use o painel abaixo para testar cadastros,
                    multi-tenancy e integrações com CNPJ.
                </p>

                <div class="flex flex-col items-center space-y-3">
                    @if (Route::has('login'))
                        <div class="flex space-x-3">
                            @auth
                                <a
                                    href="{{ route('dashboard') }}"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Ir para o Dashboard
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Entrar
                                </a>

                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    >
                                        Cadastrar nova matriz
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif

                    @if (! app()->environment('production'))
                        <a
                            href="{{ route('dev.tenants.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 mt-4"
                        >
                            Painel DEV: Excluir CNPJs Matriz
                        </a>
                    @endif
                </div>
            </div>

            <div class="absolute bottom-4 text-xs text-gray-500">
                Ambiente: {{ app()->environment() }}
            </div>
        </div>
    </body>
</html>

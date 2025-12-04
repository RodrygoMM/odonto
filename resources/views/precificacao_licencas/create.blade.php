<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h1 class="text-xl font-semibold mb-4">
                        Nova versão de precificação
                    </h1>

                    @if ($errors->any())
                        <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800 text-sm">
                            <strong>Erros encontrados:</strong>
                            <ul class="list-disc list-inside mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('precificacao-licencas.store') }}" method="POST" class="space-y-4">
                        @csrf

                        @include('precificacao_licencas._form', ['botaoTexto' => 'Salvar'])
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nova Filial') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('units.store') }}">
                        @csrf

                        {{-- Nome da filial --}}
                        <div>
                            <x-input-label for="nome" :value="__('Nome da Filial')" />
                            <x-text-input
                                id="nome"
                                class="block mt-1 w-full"
                                type="text"
                                name="nome"
                                :value="old('nome')"
                                required
                                autofocus
                            />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        {{-- CNPJ da filial --}}
                        <div class="mt-4">
                            <x-input-label for="cnpj" :value="__('CNPJ da Filial')" />
                            <x-text-input
                                id="cnpj"
                                class="block mt-1 w-full"
                                type="text"
                                name="cnpj"
                                :value="old('cnpj')"
                                required
                            />
                            <x-input-error :messages="$errors->get('cnpj')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a
                                href="{{ route('units.index') }}"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Cancelar
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Salvar Filial') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

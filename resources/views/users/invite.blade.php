<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Convidar novo usuário') }}
        </h2>
    </x-slot>

    @php
        /** @var \App\Models\User $authUser */
        $authUser    = Auth::user();
        $currentUnit = $authUser?->unit;
        $isMatriz    = $currentUnit?->is_matriz ?? false;
        $tenant      = $authUser?->tenant;
        $units       = $tenant?->units ?? collect();
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <p class="text-sm text-gray-600 mb-4">
                        Preencha os dados do colaborador que receberá o convite.
                        O usuário será criado como <span class="font-semibold">ativo</span> e 
                        <span class="font-semibold">não monetizado</span> por padrão.
                    </p>

                    <form method="POST" action="{{ route('users.invite.store') }}">
                        @csrf

                        {{-- Tenant do usuário logado (multi-tenant) --}}
                        <input type="hidden" name="tenant_id" value="{{ $tenant?->id }}">

                        {{-- Unidade / CNPJ de vínculo --}}
                        <div class="mb-4">
                            <x-input-label for="unit_id" :value="__('Unidade / CNPJ')" />

                            @if ($isMatriz)
                                {{-- Matriz: pode escolher qualquer unidade da rede --}}
                                <select
                                    id="unit_id"
                                    name="unit_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    required
                                >
                                    <option value="">{{ __('Selecione uma unidade') }}</option>

                                    @foreach ($units as $unit)
                                        <option
                                            value="{{ $unit->id }}"
                                            @selected(old('unit_id') == $unit->id)
                                        >
                                            {{ $unit->cnpj }} — {{ $unit->nome }}
                                            @if ($unit->is_matriz)
                                                (Matriz)
                                            @else
                                                (Filial)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                            @else
                                {{-- Filial: CNPJ travado, sempre da própria unidade --}}
                                <x-text-input
                                    id="cnpj_exibicao"
                                    class="block mt-1 w-full bg-gray-100"
                                    type="text"
                                    :value="$currentUnit?->cnpj"
                                    disabled
                                />

                                <input type="hidden" name="unit_id" value="{{ $currentUnit?->id }}">

                                <p class="mt-1 text-xs text-gray-500">
                                    Você está vinculado à unidade <strong>{{ $currentUnit?->nome }}</strong>
                                    (CNPJ: {{ $currentUnit?->cnpj }}). Novos usuários serão criados nesta unidade.
                                </p>
                            @endif
                        </div>

                        {{-- Nome completo --}}
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nome completo')" />
                            <x-text-input
                                id="name"
                                class="block mt-1 w-full"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- CPF --}}
                        <div class="mb-4">
                            <x-input-label for="cpf" :value="__('CPF')" />
                            <x-text-input
                                id="cpf"
                                class="block mt-1 w-full"
                                type="text"
                                name="cpf"
                                :value="old('cpf')"
                                required
                            />
                            <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                O CPF será armazenado sem pontuação no banco.
                            </p>
                        </div>

                        {{-- Data de nascimento --}}
                        <div class="mb-4">
                            <x-input-label for="data_nascimento" :value="__('Data de nascimento')" />
                            <x-text-input
                                id="data_nascimento"
                                class="block mt-1 w-full"
                                type="date"
                                name="data_nascimento"
                                :value="old('data_nascimento')"
                                required
                            />
                            <x-input-error :messages="$errors->get('data_nascimento')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                Apenas usuários com 18 anos ou mais poderão ser cadastrados.
                            </p>
                        </div>

                        {{-- E-mail (para envio do convite) --}}
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('E-mail')" />
                            <x-text-input
                                id="email"
                                class="block mt-1 w-full"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                O convite será enviado para este e-mail.
                            </p>
                        </div>

                        {{-- Ativo / Monetizado (fixos no pré-cadastro) --}}
                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label :value="__('Status do usuário')" />
                                <p class="mt-1 text-sm text-gray-800">
                                    Ativo: <span class="font-semibold text-green-700">Sim</span>
                                </p>
                                <input type="hidden" name="ativo" value="1">
                            </div>

                            <div>
                                <x-input-label :value="__('Monetização')" />
                                <p class="mt-1 text-sm text-gray-800">
                                    Monetizado: <span class="font-semibold text-red-700">Não</span>
                                </p>
                                <input type="hidden" name="monetizado" value="0">
                            </div>
                        </div>

                        {{-- Botões --}}
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a
                                href="{{ route('units.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 underline"
                            >
                                {{ __('Cancelar') }}
                            </a>

                            <x-primary-button>
                                {{ __('Enviar convite') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    @php
        $cnpjReg = $unit->cnpjRegistration;
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar dados fiscais da unidade
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">
                        Identificação da unidade
                    </h3>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-700">Nome da Unidade (interno)</dt>
                            <dd>{{ $unit->nome }}</dd>
                        </div>

                        <div>
                            <dt class="font-semibold text-gray-700">CNPJ</dt>
                            <dd>{{ $unit->cnpj }}</dd>
                        </div>

                        <div>
                            <dt class="font-semibold text-gray-700">Tipo</dt>
                            <dd>
                                @if ($unit->is_matriz)
                                    Matriz
                                @else
                                    Filial
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="font-semibold text-gray-700">Status no sistema</dt>
                            <dd>
                                @if ($unit->ativo)
                                    Ativa
                                @else
                                    Inativa
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">
                        Dados fiscais (somente leitura, espelho Receita)
                    </h3>

                    @if (! $cnpjReg)
                        <p class="text-sm text-gray-600">
                            Nenhum dado fiscal encontrado para este CNPJ.  
                            Os dados serão preenchidos automaticamente em novos cadastros.
                        </p>
                    @else
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="font-semibold text-gray-700">Razão Social</dt>
                                <dd>{{ $cnpjReg->razao_social ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">Nome Fantasia (SEFAZ)</dt>
                                <dd>{{ $cnpjReg->nome_fantasia ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">Porte</dt>
                                <dd>{{ $cnpjReg->porte ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">Tipo de Estabelecimento</dt>
                                <dd>{{ $cnpjReg->tipo_estabelecimento ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">Data de Abertura</dt>
                                <dd>{{ optional($cnpjReg->data_abertura)->format('d/m/Y') ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">CNAE Principal</dt>
                                <dd>
                                    @if ($cnpjReg->cnae_principal_codigo || $cnpjReg->cnae_principal_descricao)
                                        {{ $cnpjReg->cnae_principal_codigo }}
                                        @if ($cnpjReg->cnae_principal_descricao)
                                            - {{ $cnpjReg->cnae_principal_descricao }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="font-semibold text-gray-700">Endereço</dt>
                                <dd>
                                    {{ $cnpjReg->logradouro ?? '' }}
                                    @if ($cnpjReg->numero) , {{ $cnpjReg->numero }} @endif
                                    @if ($cnpjReg->complemento) - {{ $cnpjReg->complemento }} @endif
                                    @if ($cnpjReg->bairro) - {{ $cnpjReg->bairro }} @endif
                                    @if ($cnpjReg->municipio) - {{ $cnpjReg->municipio }} @endif
                                    @if ($cnpjReg->uf) / {{ $cnpjReg->uf }} @endif
                                    @if ($cnpjReg->cep) - CEP {{ $cnpjReg->cep }} @endif
                                </dd>
                            </div>
                        </dl>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">
                        Ajustes permitidos
                    </h3>

                    <form method="POST" action="{{ route('units.update', $unit) }}">
                        @csrf
                        @method('PUT')

                        {{-- E-mail --}}
                        <div>
                            <x-input-label for="email" :value="__('E-mail')" />
                            <x-text-input
                                id="email"
                                class="block mt-1 w-full"
                                type="email"
                                name="email"
                                :value="old('email', $cnpjReg->email ?? '')"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Telefone --}}
                        <div class="mt-4">
                            <x-input-label for="telefone" :value="__('Telefone')" />
                            <x-text-input
                                id="telefone"
                                class="block mt-1 w-full"
                                type="text"
                                name="telefone"
                                :value="old('telefone', $cnpjReg->telefone ?? '')"
                            />
                            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                        </div>

                        {{-- Situação cadastral (override manual) --}}
                        <div class="mt-4">
                            <x-input-label for="situacao_cadastral" :value="__('Situação Cadastral')" />
                            <x-text-input
                                id="situacao_cadastral"
                                class="block mt-1 w-full"
                                type="text"
                                name="situacao_cadastral"
                                :value="old('situacao_cadastral', $cnpjReg->situacao_cadastral ?? '')"
                            />
                            <x-input-error :messages="$errors->get('situacao_cadastral')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                Se a Receita atualizar a situação e você quiser refletir manualmente,
                                ajuste este campo aqui.
                            </p>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a
                                href="{{ route('units.show', $unit) }}"
                                class="text-sm text-gray-600 hover:text-gray-900 underline"
                            >
                                Cancelar
                            </a>

                            <x-primary-button>
                                {{ __('Salvar alterações') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

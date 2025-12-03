<x-app-layout>
    @php
        $cnpjReg = $unit->cnpjRegistration;
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $unit->nome }}
            <span class="text-sm text-gray-500 ml-2">
                ({{ $unit->cnpj }})
            </span>

            @if ($unit->is_matriz)
                <span class="ml-2 text-xs font-semibold text-indigo-700">Matriz</span>
            @else
                <span class="ml-2 text-xs text-gray-700">Filial</span>
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">
                        Dados fiscais (CNPJ)
                    </h3>

                    @if (! $cnpjReg)
                        <p class="text-sm text-gray-600">
                            Nenhum dado fiscal encontrado para este CNPJ.
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
                                <dt class="font-semibold text-gray-700">CNAEs Secundários</dt>
                                <dd>
                                    @if (is_array($cnpjReg->cnaes_secundarios) && count($cnpjReg->cnaes_secundarios))
                                        <ul class="list-disc list-inside">
                                            @foreach ($cnpjReg->cnaes_secundarios as $cnae)
                                                <li>
                                                    {{ $cnae['codigo'] ?? '' }}
                                                    @if (!empty($cnae['descricao']))
                                                        - {{ $cnae['descricao'] }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="font-semibold text-gray-700">Natureza Jurídica</dt>
                                <dd>
                                    @if ($cnpjReg->natureza_juridica_codigo || $cnpjReg->natureza_juridica_descricao)
                                        {{ $cnpjReg->natureza_juridica_codigo }}
                                        @if ($cnpjReg->natureza_juridica_descricao)
                                            - {{ $cnpjReg->natureza_juridica_descricao }}
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

                            <div>
                                <dt class="font-semibold text-gray-700">E-mail</dt>
                                <dd>{{ $cnpjReg->email ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">Telefone</dt>
                                <dd>{{ $cnpjReg->telefone ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">Situação Cadastral</dt>
                                <dd>{{ $cnpjReg->situacao_cadastral ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">Data Situação Cadastral</dt>
                                <dd>{{ optional($cnpjReg->data_situacao_cadastral)->format('d/m/Y') ?? '—' }}</dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="font-semibold text-gray-700">Motivo Situação Cadastral</dt>
                                <dd>{{ $cnpjReg->motivo_situacao_cadastral ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">Situação Especial</dt>
                                <dd>{{ $cnpjReg->situacao_especial ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt class="font-semibold text-gray-700">Data Situação Especial</dt>
                                <dd>{{ optional($cnpjReg->data_situacao_especial)->format('d/m/Y') ?? '—' }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>
            </div>

            <div class="flex justify-between items-center">
                <a
                    href="{{ route('units.index') }}"
                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                >
                    Voltar para Rede do Cliente
                </a>

                <a
                    href="{{ route('units.edit', $unit) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Editar unidade
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    @php
        $user   = auth()->user();
        $tenant = $user?->tenant;
        $unit   = $user?->unit;
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Título principal --}}
            {{ __('Dashboard') }}

            {{-- Nome da rede / clínica (Tenant) --}}
            @if ($tenant)
                <span class="text-sm text-gray-500 ml-2">
                    — {{ $tenant->nome_fantasia }}
                </span>
            @endif

            {{-- Unidade (Matriz / Filial) --}}
            @if ($unit)
                <span class="text-xs text-gray-500 ml-2">
                    ({{ $unit->nome }} @if($unit->is_matriz) - Matriz @else - Filial @endif)
                </span>
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    @if ($tenant)
                        <div class="mt-4 text-sm text-gray-700">
                            <p>Rede: <strong>{{ $tenant->nome_fantasia }}</strong></p>
                        </div>
                    @endif

                    @if ($unit)
                        <div class="mt-1 text-sm text-gray-700">
                            <p>Unidade: <strong>{{ $unit->nome }}</strong>
                                @if($unit->is_matriz)
                                    (Matriz)
                                @else
                                    (Filial)
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

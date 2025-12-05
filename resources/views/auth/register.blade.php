<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nome --}}
        <div>
            <x-input-label for="name" :value="__('Nome completo')" />
            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- CPF --}}
        <div class="mt-4">
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
        </div>

        {{-- Data de nascimento --}}
        <div class="mt-4">
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
        </div>

        {{-- CNPJ (matriz) --}}
        <div class="mt-4">
            <x-input-label for="cnpj" :value="__('CNPJ (Matriz)')" />
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

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Senha --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmar senha --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirme a senha')" />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}"
            >
                {{ __('Já está registrado?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

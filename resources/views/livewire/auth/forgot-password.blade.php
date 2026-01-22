<x-layouts.auth.card>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Lupa Kata Sandi')" :description="__('Masukkan alamat email Anda untuk menerima link reset kata sandi.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                type="email"
                required
                autofocus
                placeholder="Alamat Email"
            />

            <flux:button variant="primary" type="submit" class="w-full !bg-blue-500 hover:!bg-blue-600" data-test="email-password-reset-link-button">
                {{ __('Kirim Link Reset') }}
            </flux:button>
        </form>

        <div class="text-center text-sm text-zinc-400">
            <span>{{ __('Kembali ke') }}</span>
            <flux:link :href="route('login')" wire:navigate class="text-blue-600 hover:text-blue-700">{{ __('Login') }}</flux:link>
        </div>
    </div>
</x-layouts.auth.card>

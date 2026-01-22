<x-layouts.auth.card>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Buat Akun Baru')" :description="__('Masukkan detail Anda di bawah ini untuk mendaftarkan akun.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama Lengkap"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="Alamat Email"
            />

            <!-- Password -->
            <flux:input
                name="password"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Kata Sandi"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Konfirmasi Kata Sandi"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full !bg-blue-500 hover:!bg-blue-600" data-test="register-user-button">
                    {{ __('Daftar Sekarang') }}
                </flux:button>
            </div>
        </form>

        <div class="text-center text-sm text-zinc-600">
            <span>{{ __('Sudah memiliki akun?') }}</span>
            <flux:link :href="route('login')" wire:navigate class="text-blue-600 hover:text-blue-700 font-medium">{{ __('Login') }}</flux:link>
        </div>
    </div>
</x-layouts.auth.card>

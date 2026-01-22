<x-layouts.auth.card>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Atur Ulang Kata Sandi')" :description="__('Silakan masukkan kata sandi baru Anda di bawah ini.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <flux:input
                name="email"
                value="{{ request('email') }}"
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
                placeholder="Kata Sandi Baru"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Konfirmasi Kata Sandi Baru"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full !bg-blue-500 hover:!bg-blue-600" data-test="reset-password-button">
                    {{ __('Atur Ulang Kata Sandi') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.auth.card>

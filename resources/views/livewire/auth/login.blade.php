<x-layouts.auth.card>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('SISTEM ARSIP DIGITAL')" :description="__('Kantor Kecamatan Kelekar')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input name="email" :value="old('email')" type="text" required
                autofocus autocomplete="email" placeholder="Masukan Email / NIP" />

            <!-- Password -->
            <flux:input name="password" type="password" required
                autocomplete="current-password" placeholder="Masukan Kata Sandi" viewable />

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <flux:checkbox name="remember" :label="__('Ingat saya')" :checked="old('remember')" />
                @if (Route::has('password.request'))
                    <flux:link class="text-sm text-blue-600 hover:text-blue-500" :href="route('password.request')" wire:navigate>
                        {{ __('Lupa Kata Sandi?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Submit Button -->
            <flux:button variant="primary" type="submit" class="w-full !bg-blue-500 hover:!bg-blue-600 text-white" data-test="login-button">
                {{ __('Login') }}
            </flux:button>
        </form>
    </div>
</x-layouts.auth.card>

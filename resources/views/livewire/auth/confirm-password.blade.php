<x-layouts.auth.card>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Konfirmasi Kata Sandi')"
            :description="__('Ini adalah area aman. Silakan konfirmasi kata sandi Anda sebelum melanjutkan.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('Kata Sandi')"
                type="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan Kata Sandi"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full !bg-blue-500 hover:!bg-blue-600" data-test="confirm-password-button">
                {{ __('Konfirmasi') }}
            </flux:button>
        </form>
    </div>
</x-layouts.auth.card>

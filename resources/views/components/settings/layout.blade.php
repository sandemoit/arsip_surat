<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <nav class="space-y-1" aria-label="Settings">
            <a href="{{ route('profile.edit') }}" wire:navigate 
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('profile.edit') ? 'bg-blue-50 text-blue-700' : 'text-zinc-700 hover:bg-zinc-100' }}">
                {{ __('Profile') }}
            </a>
            <a href="{{ route('user-password.edit') }}" wire:navigate 
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('user-password.edit') ? 'bg-blue-50 text-blue-700' : 'text-zinc-700 hover:bg-zinc-100' }}">
                {{ __('Password') }}
            </a>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <a href="{{ route('two-factor.show') }}" wire:navigate 
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('two-factor.show') ? 'bg-blue-50 text-blue-700' : 'text-zinc-700 hover:bg-zinc-100' }}">
                    {{ __('Two-Factor Auth') }}
                </a>
            @endif
            <a href="{{ route('appearance.edit') }}" wire:navigate 
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('appearance.edit') ? 'bg-blue-50 text-blue-700' : 'text-zinc-700 hover:bg-zinc-100' }}">
                {{ __('Appearance') }}
            </a>
        </nav>
    </div>

    <hr class="md:hidden border-zinc-200 my-4">

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-lg font-semibold text-zinc-900">{{ $heading ?? '' }}</h2>
        <p class="text-sm text-zinc-500 mt-1">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>

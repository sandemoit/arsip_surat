<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased text-stone-800">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 text-stone-800">
            <div class="flex w-full max-w-sm flex-col gap-2 text-stone-800">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md text-stone-800">
                        <x-app-logo-icon class="size-9 fill-current text-black" />
                    </span>
                    <span class="sr-only text-stone-800">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6 text-stone-800">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>

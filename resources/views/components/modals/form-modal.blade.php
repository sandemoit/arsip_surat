@props([
    'title' => '',
    'maxWidth' => 'md',
])

@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth] ?? 'max-w-md';
@endphp

<flux:modal {{ $attributes->merge(['class' => $maxWidthClass . ' !bg-white [&_[data-modal-close]]:!text-zinc-500 [&_[data-modal-close]:hover]:!bg-zinc-100 [&_[data-modal-close]:hover]:!text-zinc-700']) }}>
    <div class="space-y-4 bg-white text-zinc-900">
        @if($title)
            <h2 class="text-lg font-semibold text-zinc-900 pr-8">{{ $title }}</h2>
        @endif
        
        {{ $slot }}
    </div>
</flux:modal>

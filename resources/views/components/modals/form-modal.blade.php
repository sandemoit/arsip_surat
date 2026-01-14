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

<flux:modal {{ $attributes->merge(['class' => $maxWidthClass]) }}>
    <div class="space-y-4">
        @if($title)
            <flux:heading size="lg">{{ $title }}</flux:heading>
        @endif
        
        {{ $slot }}
    </div>
</flux:modal>

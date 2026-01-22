@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, ghost, success, outline
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'loadingText' => null,
    'disabled' => false,
    'wireClick' => null,
    'wireTarget' => null, // Only set this if you want loading spinner
    'alpineClick' => null, // For instant client-side actions like closing modals
])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed';

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
][$size] ?? 'px-4 py-2 text-sm';

$variantClasses = [
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
    'secondary' => 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200',
    'success' => 'bg-green-500 text-white hover:bg-green-600',
    'danger' => 'bg-red-600 text-white hover:bg-red-700',
    'ghost' => 'bg-transparent text-zinc-600 hover:bg-zinc-100',
    'outline' => 'bg-white border border-zinc-300 text-zinc-700 hover:bg-zinc-50',
][$variant] ?? 'bg-blue-600 text-white hover:bg-blue-700';

// Only show loading if wireTarget is explicitly set
$showLoading = !empty($wireTarget);
@endphp

<button 
    type="{{ $type }}"
    @if($alpineClick) x-on:click="{{ $alpineClick }}" @endif
    @if($wireClick) wire:click="{{ $wireClick }}" @endif
    @if($showLoading) 
        wire:loading.attr="disabled" 
        wire:loading.class="opacity-50 cursor-not-allowed"
        wire:target="{{ $wireTarget }}"
    @endif
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $variantClasses"]) }}
>
    {{-- Loading Spinner (only if wireTarget is set) --}}
    @if($showLoading)
        <svg wire:loading wire:target="{{ $wireTarget }}" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif

    {{-- Icon --}}
    @if($icon && $showLoading)
        <span wire:loading.remove wire:target="{{ $wireTarget }}">
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-4 h-4" />
        </span>
    @elseif($icon)
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-4 h-4" />
    @endif

    {{-- Text --}}
    @if($showLoading && $loadingText)
        <span wire:loading.remove wire:target="{{ $wireTarget }}">{{ $slot }}</span>
        <span wire:loading wire:target="{{ $wireTarget }}">{{ $loadingText }}</span>
    @else
        {{ $slot }}
    @endif
</button>

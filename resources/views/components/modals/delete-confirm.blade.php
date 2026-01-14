@props([
    'title' => 'Konfirmasi Hapus',
    'message' => 'Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.',
    'itemName' => '',
    'confirmText' => 'Hapus',
    'cancelText' => 'Batal',
    'confirmAction' => 'delete',
    'cancelAction' => null,
    'icon' => 'exclamation-triangle',
    'iconColor' => 'red',
])

@php
$iconBgClass = [
    'red' => 'bg-red-100 dark:bg-red-900/30',
    'yellow' => 'bg-yellow-100 dark:bg-yellow-900/30',
    'orange' => 'bg-orange-100 dark:bg-orange-900/30',
][$iconColor] ?? 'bg-red-100 dark:bg-red-900/30';

$iconTextClass = [
    'red' => 'text-red-600 dark:text-red-400',
    'yellow' => 'text-yellow-600 dark:text-yellow-400',
    'orange' => 'text-orange-600 dark:text-orange-400',
][$iconColor] ?? 'text-red-600 dark:text-red-400';
@endphp

<flux:modal {{ $attributes->merge(['class' => 'max-w-sm']) }}>
    <div class="space-y-4 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full {{ $iconBgClass }}">
            <flux:icon :name="$icon" class="h-6 w-6 {{ $iconTextClass }}" />
        </div>
        
        <flux:heading size="lg">{{ $title }}</flux:heading>
        
        <flux:text class="text-zinc-600 dark:text-zinc-400">
            @if($itemName)
                {!! str_replace(':item', '<strong>"' . e($itemName) . '"</strong>', $message) !!}
            @else
                {{ $message }}
            @endif
        </flux:text>
        
        <div class="flex justify-center gap-2 pt-4">
            @if($cancelAction)
                <flux:button variant="ghost" wire:click="{{ $cancelAction }}">{{ $cancelText }}</flux:button>
            @else
                <flux:button variant="ghost" x-on:click="$dispatch('close')">{{ $cancelText }}</flux:button>
            @endif
            <flux:button variant="danger" wire:click="{{ $confirmAction }}">{{ $confirmText }}</flux:button>
        </div>
    </div>
</flux:modal>

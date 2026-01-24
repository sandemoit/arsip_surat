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
    'red' => 'bg-red-100',
    'yellow' => 'bg-yellow-100',
    'orange' => 'bg-orange-100',
][$iconColor] ?? 'bg-red-100';

$iconTextClass = [
    'red' => 'text-red-600',
    'yellow' => 'text-yellow-600',
    'orange' => 'text-orange-600',
][$iconColor] ?? 'text-red-600';
@endphp

<flux:modal {{ $attributes->merge(['class' => 'max-w-sm !bg-white [&_[data-modal-close]]:!text-zinc-500 [&_[data-modal-close]:hover]:!bg-zinc-100 [&_[data-modal-close]:hover]:!text-zinc-700']) }}>
    <div class="space-y-4 text-center bg-white">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full {{ $iconBgClass }}">
            <flux:icon :name="$icon" class="h-6 w-6 {{ $iconTextClass }}" />
        </div>
        
        <h2 class="text-lg font-semibold text-zinc-900">{{ $title }}</h2>
        
        <p class="text-zinc-600">
            @if($itemName)
                {!! str_replace(':item', '<strong>"' . e($itemName) . '"</strong>', $message) !!}
            @else
                {{ $message }}
            @endif
        </p>
        
        <div class="flex justify-center gap-2 pt-4">
            @if($cancelAction)
                <x-ui.button type="button" variant="secondary" wireClick="$set('showDeleteModal', false)">
                    {{ $cancelText }}
                </x-ui.button>
            @else
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center justify-center gap-2 font-medium rounded-lg px-4 py-2 text-sm bg-zinc-100 text-zinc-700 hover:bg-zinc-200">
                    {{ $cancelText }}
                </button>
            @endif
            <x-ui.button type="button" variant="danger" wireClick="{{ $confirmAction }}" loadingText="Menghapus...">
                {{ $confirmText }}
            </x-ui.button>
        </div>
    </div>
</flux:modal>

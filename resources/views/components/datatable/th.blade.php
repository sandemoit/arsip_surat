{{-- Table Header --}}
@props([
    'sortable' => null,
    'sortField' => null,
    'sortDirection' => 'asc',
    'center' => false,
    'right' => false,
    'w' => null,
])

@php
    $align = $center ? 'text-center justify-center' : ($right ? 'text-right justify-end' : 'text-left');
    $isSorted = $sortable && $sortable === $sortField;
    $cursor = $sortable ? 'cursor-pointer select-none hover:bg-zinc-100' : '';
@endphp

<th {{ $attributes->merge(['class' => "px-6 py-3 {$align} text-xs font-semibold text-zinc-500 uppercase tracking-wider {$cursor} transition-colors"]) }}
    @if($w) style="width: {{ $w }}px" @endif
    @if($sortable) wire:click="sortBy('{{ $sortable }}')" @endif>
    <div class="flex items-center gap-1 {{ $align }}">
        <span>{{ $slot }}</span>
        @if ($sortable)
            @if ($isSorted)
                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                </svg>
            @else
                <svg class="w-3 h-3 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                </svg>
            @endif
        @endif
    </div>
</th>

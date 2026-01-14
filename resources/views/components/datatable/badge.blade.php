{{-- Badge --}}
@props([
    'color' => 'zinc',
    'icon' => null,
])

@php
    $colors = [
        'zinc' => 'bg-zinc-100 text-zinc-700',
        'blue' => 'bg-blue-100 text-blue-700',
        'green' => 'bg-green-100 text-green-700',
        'red' => 'bg-red-100 text-red-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'purple' => 'bg-purple-100 text-purple-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
        'pink' => 'bg-pink-100 text-pink-700',
        'teal' => 'bg-teal-100 text-teal-700',
    ];
    $colorClass = $colors[$color] ?? $colors['zinc'];
    
    $icons = [
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'check' => 'M5 13l4 4L19 7',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    ];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {$colorClass}"]) }}>
    @if ($icon && isset($icons[$icon]))
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$icon] }}"/>
        </svg>
    @endif
    {{ $slot }}
</span>

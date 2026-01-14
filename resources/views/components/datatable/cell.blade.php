{{-- Table Cell --}}
@props([
    'center' => false,
    'right' => false,
    'wrap' => false,
])

@php
    $align = $center ? 'text-center' : ($right ? 'text-right' : 'text-left');
    $whitespace = $wrap ? '' : 'whitespace-nowrap';
@endphp

<td {{ $attributes->merge(['class' => "px-6 py-4 text-sm text-zinc-700 {$align} {$whitespace}"]) }}>
    {{ $slot }}
</td>

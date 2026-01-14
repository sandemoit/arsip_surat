{{-- Table Row --}}
@props(['hover' => true])

<tr {{ $attributes->merge(['class' => $hover ? 'hover:bg-zinc-50 transition-colors' : '']) }}>
    {{ $slot }}
</tr>

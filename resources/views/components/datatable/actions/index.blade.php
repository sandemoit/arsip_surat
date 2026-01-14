{{-- Actions Wrapper --}}
<div {{ $attributes->merge(['class' => 'flex items-center justify-center gap-2']) }}>
    {{ $slot }}
</div>

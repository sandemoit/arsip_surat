@props([
    'data' => [],
    'height' => '200px',
    'barColor' => 'bg-blue-500',
    'hoverColor' => 'hover:bg-blue-600',
])

@php
    $values = array_column($data, 'value');
    $maxValue = !empty($values) ? max($values) : 1;
    $maxValue = $maxValue > 0 ? $maxValue : 1;
@endphp

<div class="w-full">
    {{-- Chart Area --}}
    <div class="flex items-end gap-3 border-b border-zinc-200" style="height: {{ $height }};">
        @foreach ($data as $item)
            @php
                $percentage = ($item['value'] / $maxValue) * 100;
                $hasValue = $item['value'] > 0;
            @endphp
            <div class="flex-1 flex flex-col items-center justify-end h-full">
                {{-- Value Label --}}
                <div class="text-xs font-semibold text-zinc-600 mb-1">
                    {{ $item['value'] }}
                </div>
                {{-- Bar --}}
                <div 
                    class="w-full rounded-t transition-all cursor-pointer {{ $hasValue ? $barColor . ' ' . $hoverColor : 'bg-zinc-200' }}" 
                    style="height: {{ $hasValue ? max($percentage, 10) : 3 }}%;"
                    title="{{ $item['value'] }} {{ $item['label'] ?? 'arsip' }}"
                ></div>
            </div>
        @endforeach
    </div>
    
    {{-- X-Axis Labels --}}
    <div class="flex gap-3 pt-2">
        @foreach ($data as $item)
            <span class="flex-1 text-center text-xs text-zinc-500 font-medium">{{ $item['name'] }}</span>
        @endforeach
    </div>
</div>

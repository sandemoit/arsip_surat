@props([
    'data' => [],
    'height' => '200px',
    'barColor' => 'bg-blue-500',
    'hoverColor' => 'hover:bg-blue-600',
])

@php
    $maxValue = max(array_column($data, 'value')) ?: 1;
@endphp

<div class="w-full">
    {{-- Chart Area --}}
    <div class="flex items-end justify-between gap-2" style="height: {{ $height }};">
        @foreach ($data as $item)
            @php
                $percentage = ($item['value'] / $maxValue) * 100;
            @endphp
            <div class="flex-1 flex flex-col items-center group">
                {{-- Tooltip --}}
                <div class="relative w-full">
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-zinc-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                        {{ $item['value'] }} {{ $item['label'] ?? 'items' }}
                    </div>
                </div>
                {{-- Bar --}}
                <div 
                    class="w-full {{ $barColor }} {{ $hoverColor }} rounded-t transition-all cursor-pointer" 
                    style="height: {{ max($percentage, 5) }}%;"
                ></div>
            </div>
        @endforeach
    </div>
    
    {{-- X-Axis Labels --}}
    <div class="flex justify-between mt-2 border-t border-zinc-200 pt-2">
        @foreach ($data as $item)
            <span class="flex-1 text-center text-xs text-zinc-500">{{ $item['name'] }}</span>
        @endforeach
    </div>
</div>

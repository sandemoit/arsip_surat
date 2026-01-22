{{-- Datatable Container --}}
@props([
    'items' => null,
    'search' => 'search',
    'perPage' => 'perPage',
    'perPageOptions' => [5, 10, 25, 50],
    'searchPlaceholder' => 'Cari...',
    'showToolbar' => true,
    'showSearch' => true,
    'showPerPage' => true,
    'emptyMessage' => 'Tidak ada data',
    'emptyAction' => null,
    'emptyActionText' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden']) }}>
    {{-- Toolbar --}}
    @if ($showToolbar)
        <div class="p-4 border-b border-zinc-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if ($showPerPage)
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-zinc-600">Tampilkan</span>
                            <select wire:model.live="{{ $perPage }}" class="rounded-lg border-zinc-300 bg-white text-sm text-zinc-900 focus:ring-blue-500 focus:border-blue-500">
                                @foreach ($perPageOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            <span class="text-sm text-zinc-600">data</span>
                        </div>
                    @endif
                    @if (isset($toolbarLeft)) {{ $toolbarLeft }} @endif
                </div>
                
                <div class="flex items-center gap-4">
                    @if (isset($toolbarRight)) {{ $toolbarRight }} @endif
                    @if ($showSearch)
                        <div class="relative">
                            {{-- Search Icon / Loading Spinner --}}
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg wire:loading.remove wire:target="{{ $search }}, {{ $perPage }}" class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <svg wire:loading wire:target="{{ $search }}, {{ $perPage }}" class="w-4 h-4 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="{{ $search }}" placeholder="{{ $searchPlaceholder }}"
                                class="pl-10 pr-4 py-2 w-full md:w-64 rounded-lg border border-zinc-300 bg-white text-sm text-zinc-900 placeholder-zinc-400 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200">
            @if (isset($columns))
                <thead class="bg-zinc-50">
                    <tr>{{ $columns }}</tr>
                </thead>
            @endif
            <tbody class="bg-white divide-y divide-zinc-200">
                @if ($items && $items->isEmpty())
                    <tr>
                        <td colspan="99" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-zinc-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-zinc-500 text-sm">{{ $emptyMessage }}</p>
                                @if ($emptyAction && $emptyActionText)
                                    <button wire:click="{{ $emptyAction }}" class="mt-2 text-blue-600 text-sm hover:underline">+ {{ $emptyActionText }}</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($items && $items->hasPages())
        <div class="px-6 py-4 border-t border-zinc-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <p class="text-sm text-zinc-600">Menampilkan {{ $items->firstItem() }}-{{ $items->lastItem() }} dari {{ $items->total() }} data</p>
                <div>{{ $items->links() }}</div>
            </div>
        </div>
    @endif
</div>


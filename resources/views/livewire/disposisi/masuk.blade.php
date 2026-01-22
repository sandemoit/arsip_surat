<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">Kotak Masuk Disposisi</h1>
        <p class="text-md text-zinc-500 mt-1">Disposisi surat yang perlu ditindaklanjuti</p>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700 text-sm">{{ session('message') }}</p>
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-zinc-200 p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-zinc-900">{{ $this->stats['pending'] }}</p>
                <p class="text-sm text-zinc-500">Belum Diproses</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-zinc-200 p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-zinc-900">{{ $this->stats['selesai_today'] }}</p>
                <p class="text-sm text-zinc-500">Selesai Hari Ini</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-zinc-200 p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-zinc-900">{{ $this->stats['total'] }}</p>
                <p class="text-sm text-zinc-500">Total Disposisi</p>
            </div>
        </div>
    </div>

    {{-- Card Container --}}
    <div class="bg-white rounded-xl border border-zinc-200">
        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 p-4 border-b border-zinc-200 bg-zinc-50">
            <div class="flex gap-2">
                <button 
                    wire:click="setFilter('all')" 
                    wire:loading.attr="disabled"
                    wire:target="setFilter"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-white border border-zinc-300 text-zinc-700 hover:bg-zinc-100' }}"
                >
                    <svg wire:loading wire:target="setFilter('all')" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Semua
                </button>
                <button 
                    wire:click="setFilter('pending')" 
                    wire:loading.attr="disabled"
                    wire:target="setFilter"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ $filter === 'pending' ? 'bg-blue-600 text-white' : 'bg-white border border-zinc-300 text-zinc-700 hover:bg-zinc-100' }}"
                >
                    <svg wire:loading wire:target="setFilter('pending')" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Belum Diproses
                </button>
            </div>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari disposisi..." class="pl-9 pr-4 py-2 border border-zinc-300 rounded-lg text-sm w-64 bg-white text-zinc-900">
            </div>
        </div>

        {{-- Dispositions List --}}
        <div class="p-4 space-y-4">
            @forelse ($dispositions as $disposition)
                <div class="rounded-xl border p-5 transition-all hover:shadow-md {{ $disposition->isPending() ? 'border-l-4 border-l-blue-500 border-zinc-200 bg-blue-50/30' : 'border-zinc-200 bg-white' }}">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-zinc-900 flex items-center gap-2">
                                {{ $disposition->archive->main_meta['perihal'] ?? 'Tanpa Judul' }}
                                @if($disposition->isPending())
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-600 text-white">BARU</span>
                                @endif
                            </h3>
                            <div class="flex flex-wrap gap-4 mt-2 text-sm text-zinc-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Dari: {{ $disposition->sender->name ?? '-' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $disposition->created_at?->format('d M Y, H:i') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ $disposition->archive->main_meta['nomor_surat'] ?? '-' }}
                                </span>
                            </div>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $disposition->isPending() ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                            {{ $disposition->isPending() ? 'Pending' : 'Selesai' }}
                        </span>
                    </div>

                    {{-- Instruction Box --}}
                    <div class="bg-zinc-50 border-l-4 border-blue-500 rounded-lg p-4 mb-4">
                        <p class="text-xs font-medium text-zinc-500 uppercase mb-1">Instruksi Disposisi:</p>
                        <p class="text-zinc-700">{{ $disposition->instruction }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-2">
                        @if($disposition->isPending())
                            <button wire:click="markAsSelesai('{{ $disposition->id }}')" class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Tandai Selesai
                            </button>
                        @endif
                        <button wire:click="openViewModal('{{ $disposition->id }}')" class="px-4 py-2 text-sm font-medium rounded-lg bg-zinc-100 text-zinc-700 hover:bg-zinc-200 border border-zinc-300 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Detail
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-zinc-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-1">Tidak ada disposisi</h3>
                    <p class="text-zinc-500">Belum ada disposisi yang masuk untuk Anda</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($dispositions->hasPages())
            <div class="p-4 border-t border-zinc-200">
                {{ $dispositions->links() }}
            </div>
        @endif
    </div>

    {{-- View Modal --}}
    <x-modals.form-modal wire:model="showViewModal" title="Detail Disposisi" maxWidth="xl">
        @if($viewDisposition)
            <div class="space-y-4">
                @if($viewDisposition['archive'])
                    <div class="bg-zinc-50 rounded-xl p-4">
                        <p class="text-xs font-medium text-zinc-500 uppercase mb-2">Dokumen Terkait</p>
                        <p class="font-semibold text-zinc-900">{{ $viewDisposition['archive']['perihal'] }}</p>
                        <div class="flex gap-4 mt-2 text-sm text-zinc-500">
                            <span>{{ $viewDisposition['archive']['nomor_surat'] }}</span>
                            <span>{{ $viewDisposition['archive']['tanggal'] }}</span>
                            <span>{{ $viewDisposition['archive']['kategori'] }}</span>
                        </div>
                        @if($viewDisposition['archive']['file_path'])
                            <a href="{{ Storage::url($viewDisposition['archive']['file_path']) }}" target="_blank" class="inline-flex items-center gap-1 mt-3 text-sm text-blue-600 hover:text-blue-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download Dokumen
                            </a>
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div><p class="text-sm text-zinc-500">Pengirim</p><p class="font-medium text-zinc-900">{{ $viewDisposition['sender_name'] }}</p></div>
                    <div><p class="text-sm text-zinc-500">Tanggal Dikirim</p><p class="font-medium text-zinc-900">{{ $viewDisposition['created_at'] }}</p></div>
                    <div><p class="text-sm text-zinc-500">Status</p><p class="font-medium capitalize {{ $viewDisposition['status'] === 'pending' ? 'text-amber-600' : 'text-green-600' }}">{{ $viewDisposition['status'] }}</p></div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                    <p class="text-xs font-medium text-zinc-500 uppercase mb-1">Instruksi:</p>
                    <p class="text-zinc-700">{{ $viewDisposition['instruction'] }}</p>
                </div>

                <div class="flex justify-end pt-4"><flux:button wire:click="closeViewModal" class="bg-zinc-100 text-zinc-700 hover:bg-zinc-200">Tutup</flux:button></div>
            </div>
        @endif
    </x-modals.form-modal>
</div>

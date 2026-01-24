<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900">Riwayat Disposisi</h1>
            <p class="text-md text-zinc-500 mt-1">Histori disposisi yang telah dikirim dan diterima</p>
        </div>
        <button 
            wire:click="export" 
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="px-4 py-2 bg-white border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 flex items-center gap-2"
        >
            {{-- Loading spinner --}}
            <svg wire:loading wire:target="export" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{-- Export icon (hidden when loading) --}}
            <svg wire:loading.remove wire:target="export" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <span wire:loading.remove wire:target="export">Export</span>
            <span wire:loading wire:target="export">Mengunduh...</span>
        </button>
    </div>

    {{-- Card Container --}}
    <div class="bg-white rounded-xl border border-zinc-200">
        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 p-4 border-b border-zinc-200">
            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-500">Tampilkan</span>
                <select wire:model.live="perPage" class="px-3 py-1.5 border border-zinc-300 rounded-lg text-sm bg-white text-zinc-900">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-sm text-zinc-500">data</span>
            </div>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari riwayat..." class="pl-9 pr-4 py-2 border border-zinc-300 rounded-lg text-sm w-64 bg-white text-zinc-900">
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 border-b border-zinc-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Dokumen</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Dari/Ke</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase">Arah</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($dispositions as $index => $disposition)
                        @php
                            $isSent = $disposition->sender_id === auth()->id();
                            $otherUser = $isSent ? $disposition->receiver : $disposition->sender;
                        @endphp
                        <tr class="hover:bg-zinc-50">
                            <td class="px-4 py-4 text-sm text-zinc-600">
                                {{ $dispositions->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-medium text-zinc-900">{{ $disposition->archive->main_meta['perihal'] ?? '-' }}</p>
                                <p class="text-sm text-zinc-500">{{ $disposition->archive->main_meta['nomor_surat'] ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-600">
                                {{ $disposition->created_at?->format('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-700">
                                {{ $otherUser->name ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($isSent)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        Keluar
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                        Masuk
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($disposition->status === 'selesai')
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Selesai</span>
                                @elseif($isSent && $disposition->status === 'pending')
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">Diteruskan</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button wire:click="openViewModal('{{ $disposition->id }}')" class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 transition-colors" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-zinc-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-zinc-900 mb-1">Tidak ada riwayat</h3>
                                <p class="text-zinc-500">Belum ada riwayat disposisi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer with Pagination --}}
        <div class="flex items-center justify-between p-4 border-t border-zinc-200">
            <p class="text-sm text-zinc-500">
                Menampilkan {{ $dispositions->firstItem() ?? 0 }} - {{ $dispositions->lastItem() ?? 0 }} dari {{ $dispositions->total() }} data
            </p>
            @if($dispositions->hasPages())
                {{ $dispositions->links() }}
            @endif
        </div>
    </div>

    {{-- View Modal --}}
    <x-modals.form-modal wire:model="showViewModal" title="Detail Riwayat Disposisi" maxWidth="xl">
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
                    <div><p class="text-sm text-zinc-500">Penerima</p><p class="font-medium text-zinc-900">{{ $viewDisposition['receiver_name'] }}</p></div>
                    <div><p class="text-sm text-zinc-500">Tanggal Dikirim</p><p class="font-medium text-zinc-900">{{ $viewDisposition['created_at'] }}</p></div>
                    <div><p class="text-sm text-zinc-500">Status</p><p class="font-medium capitalize {{ $viewDisposition['status'] === 'pending' ? 'text-amber-600' : 'text-green-600' }}">{{ $viewDisposition['status'] }}</p></div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                    <p class="text-xs font-medium text-zinc-500 uppercase mb-1">Instruksi:</p>
                    <p class="text-zinc-700">{{ $viewDisposition['instruction'] }}</p>
                </div>

                <div class="flex justify-end pt-4">
                    <flux:button wire:click="$set('showViewModal', false)" class="bg-zinc-100 text-zinc-700 hover:bg-zinc-200">Tutup</flux:button>
                </div>
            </div>
        @endif
    </x-modals.form-modal>
</div>

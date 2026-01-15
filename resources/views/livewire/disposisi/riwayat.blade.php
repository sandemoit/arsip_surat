<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">Riwayat Disposisi</h1>
        <p class="text-md text-zinc-500 mt-1">Riwayat disposisi yang telah dikirim dan diterima</p>
    </div>

    {{-- Card Container --}}
    <div class="bg-white rounded-xl border border-zinc-200">
        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 p-4 border-b border-zinc-200 bg-zinc-50">
            <div class="flex gap-2">
                <button wire:click="setFilter('sent')" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'sent' ? 'bg-blue-600 text-white' : 'bg-white border border-zinc-300 text-zinc-700 hover:bg-zinc-100' }}">
                    Terkirim ({{ $sentCount }})
                </button>
                <button wire:click="setFilter('received')" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'received' ? 'bg-blue-600 text-white' : 'bg-white border border-zinc-300 text-zinc-700 hover:bg-zinc-100' }}">
                    Selesai Diterima ({{ $receivedCount }})
                </button>
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">{{ $filter === 'sent' ? 'Kepada' : 'Dari' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-zinc-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-zinc-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse ($dispositions as $disposition)
                        <tr class="hover:bg-zinc-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-zinc-900">{{ $disposition->archive->main_meta['perihal'] ?? '-' }}</p>
                                <p class="text-sm text-zinc-500">{{ $disposition->archive->main_meta['nomor_surat'] ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-zinc-700">
                                {{ $filter === 'sent' ? ($disposition->receiver->name ?? '-') : ($disposition->sender->name ?? '-') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-500">
                                {{ $disposition->created_at?->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $disposition->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $disposition->status === 'pending' ? 'Pending' : 'Selesai' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="openViewModal('{{ $disposition->id }}')" class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 transition-colors" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-zinc-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-zinc-900 mb-1">Tidak ada riwayat</h3>
                                <p class="text-zinc-500">Belum ada riwayat disposisi {{ $filter === 'sent' ? 'yang dikirim' : 'yang selesai' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($dispositions->hasPages())
            <div class="p-4 border-t border-zinc-200">
                {{ $dispositions->links() }}
            </div>
        @endif
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

                <div class="flex justify-end pt-4"><flux:button wire:click="closeViewModal" class="bg-zinc-100 text-zinc-700 hover:bg-zinc-200">Tutup</flux:button></div>
            </div>
        @endif
    </x-modals.form-modal>
</div>

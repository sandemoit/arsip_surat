<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900">Data Surat Keluar</h1>
                <p class="text-md text-zinc-500 mt-1">Kelola arsip surat keluar Kecamatan Kelekar</p>
            </div>
            <div class="flex gap-2">
                <button 
                    wire:click="export" 
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="px-4 py-2 bg-white border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 flex items-center gap-2"
                >
                    <svg wire:loading wire:target="export" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg wire:loading.remove wire:target="export" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span wire:loading.remove wire:target="export">Export</span>
                    <span wire:loading wire:target="export">Mengunduh...</span>
                </button>
                <a href="{{ route('arsip.upload') }}" class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Arsip
                </a>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700 text-sm">{{ session('message') }}</p>
        </div>
    @endif

    <x-datatable :items="$archives" searchPlaceholder="Cari surat keluar..." emptyMessage="Tidak ada data surat keluar">
        <x-slot:filters>
            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-500">Kategori:</span>
                <select wire:model.live="filterCategory" class="text-sm border border-zinc-300 rounded-lg px-3 py-1.5 bg-white text-zinc-900">
                    <option value="">Semua</option>
                    @foreach($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nama }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot:filters>

        <x-slot:columns>
            <x-datatable.th w="60">No</x-datatable.th>
            <x-datatable.th sortable="main_meta.perihal" :$sortField :$sortDirection>Dokumen</x-datatable.th>
            <x-datatable.th sortable="main_meta.tanggal" :$sortField :$sortDirection>Tanggal</x-datatable.th>
            <x-datatable.th>Tujuan/Penerima</x-datatable.th>
            <x-datatable.th center>Kategori</x-datatable.th>
            <x-datatable.th center>Status</x-datatable.th>
            <x-datatable.th center w="150">Aksi</x-datatable.th>
        </x-slot:columns>

        @forelse ($archives as $index => $archive)
            @php
                $fileType = $archive->file_info['type'] ?? 'application/octet-stream';
                $iconStyle = match(true) {
                    str_contains($fileType, 'pdf') => ['bg' => 'bg-red-100', 'text' => 'text-red-600'],
                    str_contains($fileType, 'word') || str_contains($fileType, 'doc') => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                    str_contains($fileType, 'image') => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
                    default => ['bg' => 'bg-zinc-100', 'text' => 'text-zinc-600'],
                };
                $statusBadge = $archive->statusBadge;
            @endphp
            <x-datatable.row>
                <x-datatable.cell><span class="font-medium text-blue-600">{{ $archives->firstItem() + $index }}</span></x-datatable.cell>
                <x-datatable.cell wrap>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg {{ $iconStyle['bg'] }} {{ $iconStyle['text'] }} flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-medium text-zinc-900">{{ $archive->main_meta['perihal'] ?? '-' }}</p>
                            <p class="text-sm text-zinc-500">{{ $archive->main_meta['nomor_surat'] ?? '-' }}</p>
                        </div>
                    </div>
                </x-datatable.cell>
                <x-datatable.cell>
                    @if(isset($archive->main_meta['tanggal']))
                        {{ \Carbon\Carbon::parse($archive->main_meta['tanggal'])->format('d M Y') }}
                    @else - @endif
                </x-datatable.cell>
                <x-datatable.cell>{{ $archive->main_meta['penerima'] ?? '-' }}</x-datatable.cell>
                <x-datatable.cell center>
                    @if($archive->category)
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $archive->category->badge_classes }}">{{ $archive->category->nama }}</span>
                    @else <span class="text-zinc-400">-</span> @endif
                </x-datatable.cell>
                <x-datatable.cell center>
                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }}">{{ $statusBadge['label'] }}</span>
                </x-datatable.cell>
                <x-datatable.cell center>
                    <div class="flex items-center justify-center gap-1">
                        {{-- View --}}
                        <button 
                            wire:click="openViewModal('{{ $archive->id }}')"
                            wire:loading.attr="disabled"
                            class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 transition-colors disabled:opacity-50" 
                            title="Lihat"
                        >
                            <svg wire:loading.remove wire:target="openViewModal('{{ $archive->id }}')" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg wire:loading wire:target="openViewModal('{{ $archive->id }}')" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>

                        {{-- Edit --}}
                        <a 
                            href="{{ route('arsip.edit', $archive->id) }}"
                            class="p-2 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-600 transition-colors" 
                            title="Edit"
                            wire:navigate
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>

                        {{-- Delete --}}
                        <button 
                            wire:click="confirmDelete('{{ $archive->id }}')"
                            wire:loading.attr="disabled"
                            class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition-colors disabled:opacity-50" 
                            title="Hapus"
                        >
                            <svg wire:loading.remove wire:target="confirmDelete('{{ $archive->id }}')" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <svg wire:loading wire:target="confirmDelete('{{ $archive->id }}')" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </x-datatable.cell>
            </x-datatable.row>
        @empty
        @endforelse
    </x-datatable>

    {{-- View Modal --}}
    <x-modals.form-modal wire:model="showViewModal" title="Detail Arsip" maxWidth="xl">
        @if($viewArchive)
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-4 bg-zinc-50 rounded-xl">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-zinc-900">{{ $viewArchive['file_name'] }}</p>
                        <p class="text-sm text-zinc-500">{{ $viewArchive['file_size'] }} • {{ $viewArchive['file_type'] }}</p>
                    </div>
                    @if($viewArchive['file_path'])
                        <a href="{{ Storage::url($viewArchive['file_path']) }}" target="_blank" class="px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Download</a>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><p class="text-sm text-zinc-500">Nomor Surat</p><p class="font-medium text-zinc-900">{{ $viewArchive['nomor_surat'] }}</p></div>
                    <div><p class="text-sm text-zinc-500">Tanggal</p><p class="font-medium text-zinc-900">{{ $viewArchive['tanggal'] }}</p></div>
                    <div class="col-span-2"><p class="text-sm text-zinc-500">Perihal</p><p class="font-medium text-zinc-900">{{ $viewArchive['perihal'] }}</p></div>
                    <div><p class="text-sm text-zinc-500">Pengirim</p><p class="font-medium text-zinc-900">{{ $viewArchive['pengirim'] }}</p></div>
                    <div><p class="text-sm text-zinc-500">Penerima</p><p class="font-medium text-zinc-900">{{ $viewArchive['penerima'] }}</p></div>
                    <div><p class="text-sm text-zinc-500">Kategori</p><p class="font-medium text-zinc-900">{{ $viewArchive['kategori'] }}</p></div>
                    <div><p class="text-sm text-zinc-500">Diupload oleh</p><p class="font-medium text-zinc-900">{{ $viewArchive['uploader'] }}</p></div>
                    <div class="col-span-2"><p class="text-sm text-zinc-500">Isi Ringkasan</p><p class="font-medium text-zinc-900">{{ $viewArchive['ringkasan'] }}</p></div>
                    <div class="col-span-2"><p class="text-sm text-zinc-500">Tanggal Upload</p><p class="font-medium text-zinc-900">{{ $viewArchive['created_at'] }}</p></div>
                </div>
                
                <div class="flex justify-end pt-4">
                    <flux:button wire:click="$set('showViewModal', false)" class="bg-zinc-100 text-zinc-700 hover:bg-zinc-200">Tutup</flux:button>
                </div>
            </div>
        @endif
    </x-modals.form-modal>

    <x-modals.delete-confirm wire:model="showDeleteModal" title="Hapus Arsip?" message="Apakah Anda yakin ingin menghapus :item?" :itemName="$deleteName" confirmAction="delete" cancelAction="closeDeleteModal" />
</div>

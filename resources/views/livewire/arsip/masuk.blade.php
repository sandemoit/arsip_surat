<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900">Data Surat Masuk</h1>
                <p class="text-md text-zinc-500 mt-1">Kelola arsip surat masuk Kecamatan Kelekar</p>
            </div>
            <div class="flex gap-2">
                <flux:button variant="ghost" icon="arrow-down-tray">
                    Export
                </flux:button>
                <flux:button icon="plus" href="{{ route('arsip.upload') }}" class="bg-green-500 text-white hover:bg-green-600">
                    Tambah Arsip
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700 text-sm">{{ session('message') }}</p>
        </div>
    @endif

    {{-- Table --}}
    <x-datatable 
        :items="$archives"
        searchPlaceholder="Cari surat masuk..."
        emptyMessage="Tidak ada data surat masuk"
    >
        <x-slot:filters>
            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-500">Kategori:</span>
                <select wire:model.live="filterCategory" class="text-sm border border-zinc-300 rounded-lg px-3 py-1.5 bg-white text-zinc-900">
                    <option value="">Semua</option>
                    @foreach($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot:filters>

        <x-slot:columns>
            <x-datatable.th w="60">No</x-datatable.th>
            <x-datatable.th sortable="main_meta.perihal" :$sortField :$sortDirection>Dokumen</x-datatable.th>
            <x-datatable.th sortable="main_meta.tanggal" :$sortField :$sortDirection>Tanggal</x-datatable.th>
            <x-datatable.th>Pengirim</x-datatable.th>
            <x-datatable.th center>Kategori</x-datatable.th>
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
            @endphp
            <x-datatable.row>
                <x-datatable.cell>
                    <span class="font-medium text-blue-600">{{ $archives->firstItem() + $index }}</span>
                </x-datatable.cell>
                
                <x-datatable.cell wrap>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg {{ $iconStyle['bg'] }} {{ $iconStyle['text'] }} flex items-center justify-center">
                            @if(str_contains($fileType, 'pdf'))
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1v3h-1v-1h-.5v-1h.5v-1zm2 0h1.5a1 1 0 011 1v1a1 1 0 01-1 1h-.5v1h-1v-4zm1 2h.5v-1h-.5v1zm2-2h1.5v1h-.5v1h.5v1h-1.5v-3z"/></svg>
                            @elseif(str_contains($fileType, 'image'))
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endif
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
                    @else
                        -
                    @endif
                </x-datatable.cell>
                
                <x-datatable.cell>{{ $archive->main_meta['pengirim'] ?? '-' }}</x-datatable.cell>
                
                <x-datatable.cell center>
                    @if($archive->category)
                        <x-datatable.badge color="blue">{{ $archive->category->name }}</x-datatable.badge>
                    @else
                        <span class="text-zinc-400">-</span>
                    @endif
                </x-datatable.cell>
                
                <x-datatable.cell center>
                    <div class="flex items-center justify-center gap-1">
                        {{-- View --}}
                        <button 
                            wire:click="openViewModal('{{ $archive->id }}')"
                            class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 transition-colors" 
                            title="Lihat"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        
                        {{-- Disposisi --}}
                        <button 
                            wire:click="openDisposisiModal('{{ $archive->id }}')"
                            class="p-2 rounded-lg bg-purple-100 hover:bg-purple-200 text-purple-600 transition-colors" 
                            title="Kirim Disposisi"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                        </button>
                        
                        {{-- Edit --}}
                        <button 
                            class="p-2 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-600 transition-colors" 
                            title="Edit"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                        
                        {{-- Delete --}}
                        <button 
                            wire:click="confirmDelete('{{ $archive->id }}')"
                            class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition-colors" 
                            title="Hapus"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                {{-- File Info --}}
                <div class="flex items-center gap-4 p-4 bg-zinc-50 rounded-xl">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-zinc-900">{{ $viewArchive['file_name'] }}</p>
                        <p class="text-sm text-zinc-500">{{ $viewArchive['file_size'] }} • {{ $viewArchive['file_type'] }}</p>
                    </div>
                    @if($viewArchive['file_path'])
                        <a href="{{ Storage::url($viewArchive['file_path']) }}" target="_blank" class="px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            Download
                        </a>
                    @endif
                </div>

                {{-- Details --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-zinc-500">Nomor Surat</p>
                        <p class="font-medium text-zinc-900">{{ $viewArchive['nomor_surat'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500">Tanggal</p>
                        <p class="font-medium text-zinc-900">{{ $viewArchive['tanggal'] }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-zinc-500">Perihal</p>
                        <p class="font-medium text-zinc-900">{{ $viewArchive['perihal'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500">Pengirim</p>
                        <p class="font-medium text-zinc-900">{{ $viewArchive['pengirim'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500">Penerima</p>
                        <p class="font-medium text-zinc-900">{{ $viewArchive['penerima'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500">Kategori</p>
                        <p class="font-medium text-zinc-900">{{ $viewArchive['kategori'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500">Diupload oleh</p>
                        <p class="font-medium text-zinc-900">{{ $viewArchive['uploader'] }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-zinc-500">Keterangan</p>
                        <p class="font-medium text-zinc-900">{{ $viewArchive['keterangan'] }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-zinc-500">Tanggal Upload</p>
                        <p class="font-medium text-zinc-900">{{ $viewArchive['created_at'] }}</p>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <flux:button wire:click="closeViewModal" class="bg-zinc-100 text-zinc-700 hover:bg-zinc-200">Tutup</flux:button>
                </div>
            </div>
        @endif
    </x-modals.form-modal>

    {{-- Delete Modal --}}
    <x-modals.delete-confirm 
        wire:model="showDeleteModal"
        title="Hapus Arsip?"
        message="Apakah Anda yakin ingin menghapus :item? File arsip juga akan dihapus dari sistem."
        :itemName="$deleteName"
        confirmAction="delete"
        cancelAction="closeDeleteModal"
    />

    {{-- Kirim Disposisi Modal --}}
    <x-modals.form-modal wire:model="showDisposisiModal" title="Kirim Disposisi" maxWidth="lg">
        <form wire:submit="sendDisposisi">
            <div class="space-y-4">
                {{-- Arsip Info --}}
                <div class="bg-zinc-50 rounded-lg p-4 border-l-4 border-blue-500">
                    <p class="text-xs font-medium text-zinc-500 uppercase mb-1">Dokumen:</p>
                    <p class="font-medium text-zinc-900">{{ $disposisiArchiveName }}</p>
                </div>

                {{-- Penerima --}}
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">Kirim Kepada <span class="text-red-500">*</span></label>
                    <select wire:model="disposisiReceiverId" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900">
                        <option value="">-- Pilih Penerima --</option>
                        @foreach($this->users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                        @endforeach
                    </select>
                    @error('disposisiReceiverId')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Instruksi --}}
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">Instruksi Disposisi <span class="text-red-500">*</span></label>
                    <textarea 
                        wire:model="disposisiInstruction" 
                        rows="4"
                        class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900"
                        placeholder="Tuliskan instruksi untuk penerima disposisi..."
                    ></textarea>
                    @error('disposisiInstruction')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200">
                    <flux:button type="button" wire:click="closeDisposisiModal" class="bg-zinc-100 text-zinc-700 hover:bg-zinc-200">
                        Batal
                    </flux:button>
                    <flux:button type="submit" class="bg-purple-600 text-white hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Disposisi
                    </flux:button>
                </div>
            </div>
        </form>
    </x-modals.form-modal>
</div>
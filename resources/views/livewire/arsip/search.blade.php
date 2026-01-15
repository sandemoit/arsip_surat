<div>
    {{-- Search Hero --}}
    <div class="bg-gradient-to-r from-blue-700 to-blue-500 rounded-xl p-8 mb-6 text-white">
        <h1 class="text-2xl font-bold mb-1">Pencarian Arsip</h1>
        <p class="text-blue-100 mb-6">Telusuri metadata dan isi dokumen arsip secara cepat</p>
        
        <div class="flex gap-2 bg-white rounded-lg p-1.5 shadow-lg">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Ketik nomor surat, judul, atau kata kunci..."
                class="flex-1 px-4 py-3 text-zinc-900 bg-transparent border-none focus:outline-none text-base"
            >
            <button class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Cari
            </button>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-zinc-700 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter Pencarian
            </div>
            <button wire:click="toggleFilters" class="text-blue-600 text-sm flex items-center gap-1 hover:text-blue-700">
                <span>{{ $showFilters ? 'Sembunyikan' : 'Tampilkan' }}</span>
                <svg class="w-4 h-4 transition-transform {{ $showFilters ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>
        
        @if($showFilters)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-600 mb-1.5">Jenis Surat</label>
                    <select wire:model.live="jenisSurat" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 text-sm">
                        <option value="">Semua Jenis</option>
                        <option value="masuk">Surat Masuk</option>
                        <option value="keluar">Surat Keluar</option>
                        <option value="sk">SK / Keputusan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-600 mb-1.5">Kategori</label>
                    <select wire:model.live="categoryId" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-600 mb-1.5">Dari Tanggal</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-600 mb-1.5">Sampai Tanggal</label>
                    <input type="date" wire:model.live="dateTo" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 text-sm">
                </div>
            </div>
            @if($search || $jenisSurat || $categoryId || $dateFrom || $dateTo)
                <div class="mt-4 pt-4 border-t border-zinc-100">
                    <button wire:click="resetFilters" class="text-sm text-red-600 hover:text-red-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset Filter
                    </button>
                </div>
            @endif
        @endif
    </div>

    {{-- Results Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="text-sm text-zinc-600">
            Menampilkan <strong class="text-zinc-900">{{ $archives->count() }}</strong> dari <strong class="text-zinc-900">{{ $archives->total() }}</strong> arsip
        </div>
    </div>

    {{-- Results --}}
    <div class="space-y-3">
        @forelse ($archives as $archive)
            @php
                $fileType = $archive->file_info['type'] ?? 'application/octet-stream';
                $iconStyle = match(true) {
                    str_contains($fileType, 'pdf') => ['bg' => 'bg-red-100', 'text' => 'text-red-600'],
                    str_contains($fileType, 'word') || str_contains($fileType, 'doc') => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                    str_contains($fileType, 'image') => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
                    default => ['bg' => 'bg-zinc-100', 'text' => 'text-zinc-600'],
                };
                $jenisLabel = match($archive->jenis_surat) {
                    'masuk' => ['label' => 'Surat Masuk', 'color' => 'blue'],
                    'keluar' => ['label' => 'Surat Keluar', 'color' => 'green'],
                    'sk' => ['label' => 'SK', 'color' => 'purple'],
                    default => ['label' => 'Lainnya', 'color' => 'zinc'],
                };
            @endphp
            <div class="bg-white rounded-xl border border-zinc-200 p-5 hover:border-blue-300 hover:shadow-md transition-all flex gap-4">
                {{-- Icon --}}
                <div class="flex-shrink-0 w-14 h-14 rounded-lg {{ $iconStyle['bg'] }} {{ $iconStyle['text'] }} flex items-center justify-center">
                    @if(str_contains($fileType, 'pdf'))
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4z"/></svg>
                    @elseif(str_contains($fileType, 'image'))
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @else
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @endif
                </div>
                
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <a href="#" wire:click.prevent="openViewModal('{{ $archive->id }}')" class="font-semibold text-zinc-900 hover:text-blue-600">
                            {{ $archive->main_meta['perihal'] ?? 'Tanpa Judul' }}
                        </a>
                        @if($archive->category)
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                                @if($jenisLabel['color'] == 'blue') bg-blue-100 text-blue-700
                                @elseif($jenisLabel['color'] == 'green') bg-green-100 text-green-700
                                @elseif($jenisLabel['color'] == 'purple') bg-purple-100 text-purple-700
                                @else bg-zinc-100 text-zinc-700 @endif">
                                {{ $archive->category->name }}
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-4 text-sm text-zinc-500 mb-2">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ $archive->main_meta['nomor_surat'] ?? '-' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @if(isset($archive->main_meta['tanggal']))
                                {{ \Carbon\Carbon::parse($archive->main_meta['tanggal'])->format('d M Y') }}
                            @else - @endif
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $archive->main_meta['pengirim'] ?? $archive->main_meta['penerima'] ?? '-' }}
                        </span>
                    </div>
                    @if(isset($archive->main_meta['keterangan']) && $archive->main_meta['keterangan'])
                        <p class="text-sm text-zinc-500 line-clamp-2">{{ $archive->main_meta['keterangan'] }}</p>
                    @endif
                </div>
                
                {{-- Actions --}}
                <div class="flex flex-col gap-2">
                    <button wire:click="openViewModal('{{ $archive->id }}')" class="p-2 rounded-lg bg-zinc-100 hover:bg-blue-600 hover:text-white text-zinc-600 transition-colors" title="Lihat Detail">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    @if(isset($archive->file_info['path']))
                        <a href="{{ Storage::url($archive->file_info['path']) }}" target="_blank" class="p-2 rounded-lg bg-zinc-100 hover:bg-blue-600 hover:text-white text-zinc-600 transition-colors" title="Download">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-zinc-200 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-zinc-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 mb-1">Tidak ada hasil</h3>
                <p class="text-zinc-500">Coba ubah kata kunci atau filter pencarian Anda</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($archives->hasPages())
        <div class="mt-6">
            {{ $archives->links() }}
        </div>
    @endif

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
                    <div><p class="text-sm text-zinc-500">Jenis Surat</p><p class="font-medium text-zinc-900 capitalize">{{ $viewArchive['jenis_surat'] }}</p></div>
                    <div class="col-span-2"><p class="text-sm text-zinc-500">Keterangan</p><p class="font-medium text-zinc-900">{{ $viewArchive['keterangan'] }}</p></div>
                    <div class="col-span-2"><p class="text-sm text-zinc-500">Diupload oleh</p><p class="font-medium text-zinc-900">{{ $viewArchive['uploader'] }} • {{ $viewArchive['created_at'] }}</p></div>
                </div>
                <div class="flex justify-end pt-4"><flux:button wire:click="closeViewModal" class="bg-zinc-100 text-zinc-700 hover:bg-zinc-200">Tutup</flux:button></div>
            </div>
        @endif
    </x-modals.form-modal>
</div>
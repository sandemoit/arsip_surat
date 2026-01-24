<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">{{ $editId ? 'Edit Arsip' : 'Upload Arsip Baru' }}</h1>
        <p class="text-md text-zinc-500 mt-1">{{ $editId ? 'Perbarui informasi dokumen arsip.' : 'Unggah dokumen arsip ke dalam sistem.' }}</p>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-700 text-sm font-medium">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm">
        <div class="px-6 py-4 border-b border-zinc-200">
            <h2 class="text-lg font-semibold text-zinc-900">{{ $editId ? 'Form Edit Arsip' : 'Form Upload Arsip' }}</h2>
        </div>

        <div class="p-6">
            <form wire:submit="save">
                {{-- Upload Zone --}}
                <div class="mb-6">
                    @if($editId && !$file)
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">File Saat Ini Terlampir</p>
                                    <p class="text-xs text-blue-700">{{ $archive->file_info['original_name'] ?? 'Dokumen' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="{{ Storage::url($archive->file_info['path'] ?? '') }}" target="_blank" class="text-sm text-blue-700 hover:underline block">Lihat File</a>
                            </div>
                        </div>
                        <p class="text-sm text-zinc-500 mb-2">Ingin mengganti file? Upload file baru di bawah ini:</p>
                    @endif

                    @if (!$file)
                        <div
                            x-data="{ 
                                dragover: false,
                                handleDrop(e) {
                                    this.dragover = false;
                                    const files = e.dataTransfer.files;
                                    if (files.length) {
                                        $refs.fileInput.files = files;
                                        $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                }
                            }"
                            x-on:dragover.prevent="dragover = true"
                            x-on:dragleave.prevent="dragover = false"
                            x-on:drop.prevent="handleDrop($event)"
                            x-on:click="$refs.fileInput.click()"
                            :class="dragover ? 'border-blue-500 bg-blue-50' : 'border-zinc-300 bg-zinc-50 hover:border-blue-400 hover:bg-blue-50/50'"
                            class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all duration-200"
                        >
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-zinc-700">{{ $editId ? 'Ganti File (Opsional)' : 'Drag & Drop file di sini' }}</p>
                                    <p class="text-sm text-zinc-500 mt-1">atau klik untuk memilih file</p>
                                </div>
                                <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $editId ? 'Pilih File Baru' : 'Pilih File' }}
                                </button>
                                <p class="text-xs text-zinc-400 mt-2">Format yang didukung: PDF, DOC, DOCX, JPG, PNG (Maks. 10MB)</p>
                            </div>
                            <input 
                                type="file" 
                                x-ref="fileInput" 
                                wire:model="file" 
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                class="hidden"
                            >
                        </div>
                    @else
                        {{-- File Preview --}}
                        <div class="flex items-center gap-4 p-4 bg-zinc-50 rounded-xl border border-zinc-200">
                            @php
                                $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                                $iconClass = match($ext) {
                                    'pdf' => 'bg-red-100 text-red-600',
                                    'doc', 'docx' => 'bg-blue-100 text-blue-600',
                                    'jpg', 'jpeg', 'png' => 'bg-green-100 text-green-600',
                                    default => 'bg-zinc-100 text-zinc-600'
                                };
                                $icon = match($ext) {
                                    'pdf' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                                    'doc', 'docx' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                    default => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'
                                };
                            @endphp
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg {{ $iconClass }} flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-zinc-900 truncate">{{ $file->getClientOriginalName() }}</p>
                                <p class="text-xs text-zinc-500">{{ number_format($file->getSize() / 1024 / 1024, 2) }} MB</p>
                            </div>
                            <button 
                                type="button" 
                                wire:click="removeFile"
                                class="p-2 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    {{-- File Upload Error --}}
                    @error('file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Loading indicator --}}
                    <div wire:loading wire:target="file" class="mt-2 flex items-center gap-2 text-sm text-blue-600">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengupload file...</span>
                    </div>
                </div>

                {{-- Form Section: Informasi Arsip --}}
                <div class="border-t border-zinc-200 pt-6">
                    <h3 class="text-base font-semibold text-zinc-900 mb-4 pb-2 border-b border-zinc-100">Informasi Arsip</h3>

                    <div class="space-y-4">
                        {{-- Row 1: Nomor Surat & Tanggal --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 mb-1">Nomor Surat / Dokumen <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="nomor_surat" placeholder="Contoh: 005/123/KC/2025" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('nomor_surat') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="tanggal_surat" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('tanggal_surat') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Row 2: Perihal --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Judul / Perihal Surat <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="perihal" placeholder="Masukkan judul atau perihal surat" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('perihal') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Row 3: Jenis & Kategori --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 mb-1">Jenis Surat <span class="text-red-500">*</span></label>
                                <select wire:model="jenis_surat" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Pilih Jenis Surat --</option>
                                    @foreach($jenisOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_surat') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                <select wire:model="category_id" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($this->categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->code }} - {{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Row 4: Pengirim & Penerima --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 mb-1">Pengirim / Asal Surat</label>
                                <input type="text" wire:model="pengirim" placeholder="Nama instansi atau perorangan" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('pengirim') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 mb-1">Tujuan / Penerima</label>
                                <input type="text" wire:model="penerima" placeholder="Nama penerima surat" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('penerima') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Row 5: Keterangan --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Keterangan Tambahan</label>
                            <textarea wire:model="keterangan" placeholder="Tambahkan catatan atau keterangan jika diperlukan..." rows="3" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('keterangan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-zinc-200">
                    <flux:button type="button" wire:click="cancel" class="bg-zinc-100 text-zinc-700 hover:bg-zinc-200 border border-zinc-300">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </flux:button>
                    <flux:button type="submit" wire:loading.attr="disabled" class="bg-blue-600 text-white hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-1" wire:loading.remove wire:target="save" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <svg class="w-4 h-4 mr-1 animate-spin" wire:loading wire:target="save" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Upload Arsip
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>

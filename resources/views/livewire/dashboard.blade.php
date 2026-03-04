<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">
            Selamat Datang, {{ Auth::user()->name }}!
        </h1>
        <p class="text-md text-zinc-500 mt-1">
            @if($this->isAdmin)
                Overview data arsip Kecamatan Kelekar hari ini.
            @else
                Ringkasan aktivitas arsip Anda.
            @endif
        </p>
    </div>

    {{-- Stats Cards --}}
    @if($this->isAdmin)
        {{-- ADMIN STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-zinc-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-zinc-900">{{ number_format($this->stats['total_arsip']) }}</p>
                    <p class="text-sm text-zinc-500 mt-1">Total Arsip</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-zinc-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-zinc-900">{{ $this->stats['arsip_hari_ini'] }}</p>
                    <p class="text-sm text-zinc-500 mt-1">Arsip Hari Ini</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-zinc-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-zinc-900">{{ $this->stats['disposisi_pending'] }}</p>
                    <p class="text-sm text-zinc-500 mt-1">Disposisi Pending</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-zinc-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-zinc-900">{{ $this->stats['total_users'] }}</p>
                    <p class="text-sm text-zinc-500 mt-1">Total Pengguna</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Admin: Ringkasan Jenis Surat --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <a href="{{ route('arsip.masuk') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-5 text-white hover:from-blue-600 hover:to-blue-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Surat Masuk</p>
                        <p class="text-3xl font-bold mt-1">{{ $this->stats['surat_masuk'] }}</p>
                    </div>
                    <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
            </a>
            <a href="{{ route('arsip.keluar') }}" class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-5 text-white hover:from-green-600 hover:to-green-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Surat Keluar</p>
                        <p class="text-3xl font-bold mt-1">{{ $this->stats['surat_keluar'] }}</p>
                    </div>
                    <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
            </a>
            <a href="{{ route('kategori.index') }}" class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-5 text-white hover:from-purple-600 hover:to-purple-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Kategori Arsip</p>
                        <p class="text-3xl font-bold mt-1">{{ $this->stats['total_kategori'] }}</p>
                    </div>
                    <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </a>
        </div>
    @else
        {{-- STAF STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-zinc-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-zinc-900">{{ $this->stats['arsip_diupload'] }}</p>
                    <p class="text-sm text-zinc-500 mt-1">Arsip Saya</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('disposisi.masuk') }}" class="bg-white rounded-xl border border-zinc-200 p-5 flex items-center justify-between hover:border-amber-300 hover:shadow-md transition-all">
                <div>
                    <p class="text-3xl font-bold text-amber-600">{{ $this->stats['disposisi_masuk'] }}</p>
                    <p class="text-sm text-zinc-500 mt-1">Disposisi Masuk</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
            </a>
            <div class="bg-white rounded-xl border border-zinc-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-green-600">{{ $this->stats['disposisi_selesai'] }}</p>
                    <p class="text-sm text-zinc-500 mt-1">Disposisi Selesai</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Aktivitas Terbaru - Table Style --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200">
            <div class="flex items-center justify-between p-4 border-b border-zinc-200">
                <h3 class="font-semibold text-zinc-900">Aktivitas Terbaru</h3>
                <a href="{{ route('arsip.search') }}" class="px-3 py-1 text-sm text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 border-b border-zinc-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Judul Surat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Waktu</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($this->recentArchives as $archive)
                            @php
                                $categoryColors = [
                                    'Umum' => 'bg-blue-100 text-blue-700',
                                    'Penduduk' => 'bg-amber-100 text-amber-700',
                                    'Keuangan' => 'bg-green-100 text-green-700',
                                ];
                                $badgeColor = $categoryColors[$archive->category->nama ?? ''] ?? 'bg-zinc-100 text-zinc-700';
                            @endphp
                            <tr class="hover:bg-zinc-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-blue-600">{{ $archive->main_meta['perihal'] ?? '-' }}</p>
                                    <p class="text-xs text-zinc-500">{{ $archive->main_meta['nomor_surat'] ?? ($archive->main_meta['pengirim'] ? 'Dari: ' . $archive->main_meta['pengirim'] : '-') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($archive->category)
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $badgeColor }}">
                                            {{ $archive->category->nama }}
                                        </span>
                                    @else
                                        <span class="text-zinc-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-600">
                                    {{ $archive->created_at?->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <svg class="w-5 h-5 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-zinc-500">
                                    Belum ada arsip
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Grafik Arsip Bulanan --}}
        <div class="bg-white rounded-xl border border-zinc-200">
            <div class="p-4 border-b border-zinc-200">
                <h3 class="font-semibold text-zinc-900">Grafik Arsip Bulanan</h3>
            </div>
            <div class="p-6">
                <x-charts.bar-chart :data="$this->monthlyStats" height="180px" />
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6">
        <h3 class="font-semibold text-zinc-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('arsip.upload') }}" class="bg-white rounded-xl border border-zinc-200 p-4 text-center hover:border-blue-300 hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <p class="font-medium text-zinc-900">Upload Arsip</p>
            </a>
            <a href="{{ route('arsip.search') }}" class="bg-white rounded-xl border border-zinc-200 p-4 text-center hover:border-green-300 hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-3 group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <p class="font-medium text-zinc-900">Cari Arsip</p>
            </a>
            <a href="{{ route('arsip.masuk') }}" class="bg-white rounded-xl border border-zinc-200 p-4 text-center hover:border-purple-300 hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mx-auto mb-3 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <p class="font-medium text-zinc-900">Surat Masuk</p>
            </a>
            <a href="{{ route('disposisi.masuk') }}" class="bg-white rounded-xl border border-zinc-200 p-4 text-center hover:border-amber-300 hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-3 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                </div>
                <p class="font-medium text-zinc-900">Disposisi</p>
            </a>
        </div>
    </div>
</div>

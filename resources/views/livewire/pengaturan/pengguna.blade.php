<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900">Kelola Data Pengguna</h1>
                <p class="text-md text-zinc-500 mt-1">Manajemen akun pengguna sistem arsip digital</p>
            </div>
            <flux:button icon="plus" class="bg-green-500 text-white hover:bg-green-600">
                Tambah Pengguna
            </flux:button>
        </div>
    </div>

    {{-- Table --}}
    <x-datatable 
        :items="$users"
        searchPlaceholder="Cari pengguna..."
        emptyMessage="Tidak ada data pengguna"
    >
        <x-slot:columns>
            <x-datatable.th w="60">No</x-datatable.th>
            <x-datatable.th>Nama & Email</x-datatable.th>
            <x-datatable.th>NIP</x-datatable.th>
            <x-datatable.th center>Role</x-datatable.th>
            <x-datatable.th center w="150">Aksi</x-datatable.th>
        </x-slot:columns>

        @foreach ($users as $index => $user)
            <x-datatable.row>
                <x-datatable.cell>
                    <span class="font-medium text-blue-600">{{ $users->firstItem() + $index }}</span>
                </x-datatable.cell>
                
                <x-datatable.cell wrap>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 text-sm font-semibold flex-shrink-0">
                            {{ $user->initials() }}
                        </div>
                        <div>
                            <p class="font-medium text-zinc-900">{{ $user->name }}</p>
                            <p class="text-sm text-zinc-500">{{ $user->email }}</p>
                        </div>
                    </div>
                </x-datatable.cell>
                
                <x-datatable.cell>{{ $user->nip ?? '-' }}</x-datatable.cell>
                
                <x-datatable.cell center>
                    @if($user->role === 'admin')
                        <x-datatable.badge color="green">Admin</x-datatable.badge>
                    @else
                        <x-datatable.badge color="blue">Staf</x-datatable.badge>
                    @endif
                </x-datatable.cell>
                
                <x-datatable.cell center>
                    <x-datatable.actions edit="openEditModal('{{ $user->id }}')" delete="confirmDelete('{{ $user->id }}')">
                        <button class="p-2 rounded-lg bg-purple-100 hover:bg-purple-200 text-purple-600 transition-colors" title="Reset Password">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </button>
                    </x-datatable.actions>
                </x-datatable.cell>
            </x-datatable.row>
        @endforeach
    </x-datatable>
</div>

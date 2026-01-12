<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Kelola Data Pengguna</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Manajemen akun pengguna sistem arsip digital</flux:text>
        </div>
        <flux:button icon="plus" variant="primary">
            Tambah Pengguna
        </flux:button>
    </div>

    {{-- Filters --}}
    <div class="flex items-center justify-between mb-4 gap-4">
        <div class="flex items-center gap-2">
            <flux:text>Tampilkan</flux:text>
            <flux:select wire:model.live="perPage" class="w-20">
                <flux:select.option value="10">10</flux:select.option>
                <flux:select.option value="25">25</flux:select.option>
                <flux:select.option value="50">50</flux:select.option>
            </flux:select>
            <flux:text>data</flux:text>
        </div>
        <flux:input 
            icon="magnifying-glass" 
            placeholder="Cari pengguna..." 
            wire:model.live.debounce.300ms="search" 
            class="w-64"
        />
    </div>

    {{-- Table --}}
    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>No</flux:table.column>
            <flux:table.column>Nama & Email</flux:table.column>
            <flux:table.column>NIP</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($users as $index => $user)
                <flux:table.row>
                    <flux:table.cell>{{ $users->firstItem() + $index }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700 text-sm font-semibold">
                                {{ $user->initials() }}
                            </div>
                            <div>
                                <div class="font-medium">{{ $user->name }}</div>
                                <div class="text-sm text-zinc-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->nip ?? '-' }}</flux:table.cell>
                    <flux:table.cell>
                        @if($user->role === 'admin')
                            <flux:badge color="green" size="sm">Admin</flux:badge>
                        @else
                            <flux:badge color="blue" size="sm">Staf</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-1">
                            <flux:button icon="pencil" size="sm" variant="ghost" />
                            <flux:button icon="key" size="sm" variant="ghost" />
                            <flux:button icon="trash" size="sm" variant="ghost" class="text-red-500 hover:text-red-700" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">
                        Tidak ada data pengguna.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>

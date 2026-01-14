<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900">Kelola Data Pengguna</h1>
                <p class="text-md text-zinc-500 mt-1">Manajemen akun pengguna sistem arsip digital</p>
            </div>
            <flux:button icon="plus" class="bg-green-500 text-white hover:bg-green-600" wire:click="openCreateModal">
                Tambah Pengguna
            </flux:button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700 text-sm">{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700 text-sm">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Table --}}
    <x-datatable 
        :items="$users"
        searchPlaceholder="Cari pengguna..."
        emptyMessage="Tidak ada data pengguna"
        emptyAction="openCreateModal"
        emptyActionText="Tambah pengguna baru"
    >
        <x-slot:columns>
            <x-datatable.th w="60">No</x-datatable.th>
            <x-datatable.th sortable="name" :$sortField :$sortDirection>Nama & Email</x-datatable.th>
            <x-datatable.th sortable="nip" :$sortField :$sortDirection>NIP</x-datatable.th>
            <x-datatable.th sortable="role" :$sortField :$sortDirection center>Role</x-datatable.th>
            <x-datatable.th center>Status</x-datatable.th>
            <x-datatable.th center>Terakhir Login</x-datatable.th>
            <x-datatable.th center w="180">Aksi</x-datatable.th>
        </x-slot:columns>

        @foreach ($users as $index => $user)
            <x-datatable.row>
                <x-datatable.cell>
                    <span class="font-medium text-blue-600">{{ $users->firstItem() + $index }}</span>
                </x-datatable.cell>
                
                <x-datatable.cell wrap>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-semibold flex-shrink-0">
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
                        <x-datatable.badge color="red" icon="shield-check">Admin</x-datatable.badge>
                    @else
                        <x-datatable.badge color="blue" icon="user">Staf</x-datatable.badge>
                    @endif
                </x-datatable.cell>
                
                <x-datatable.cell center>
                    @if($user->is_active)
                        <x-datatable.badge color="green" icon="check-circle">Aktif</x-datatable.badge>
                    @else
                        <x-datatable.badge color="zinc" icon="x-circle">Nonaktif</x-datatable.badge>
                    @endif
                </x-datatable.cell>
                
                <x-datatable.cell center>
                    @if($user->last_login_at)
                        <span class="text-sm text-zinc-600">
                            {{ \Carbon\Carbon::parse($user->last_login_at)->format('d M Y, H:i') }}
                        </span>
                    @else
                        <span class="text-sm text-zinc-400">Belum pernah</span>
                    @endif
                </x-datatable.cell>
                
                <x-datatable.cell center>
                    <div class="flex items-center justify-center gap-1">
                        {{-- Edit --}}
                        <button 
                            wire:click="openEditModal('{{ $user->id }}')"
                            class="p-2 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-600 transition-colors" 
                            title="Edit"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                        
                        {{-- Reset Password --}}
                        <button 
                            wire:click="openResetPasswordModal('{{ $user->id }}')"
                            class="p-2 rounded-lg bg-purple-100 hover:bg-purple-200 text-purple-600 transition-colors" 
                            title="Reset Password"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </button>
                        
                        {{-- Toggle Active --}}
                        @if($user->id !== auth()->id())
                            <button 
                                wire:click="toggleActive('{{ $user->id }}')"
                                class="p-2 rounded-lg {{ $user->is_active ? 'bg-zinc-100 hover:bg-zinc-200 text-zinc-600' : 'bg-green-100 hover:bg-green-200 text-green-600' }} transition-colors" 
                                title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                            >
                                @if($user->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </button>
                        @endif
                        
                        {{-- Delete --}}
                        @if($user->id !== auth()->id())
                            <button 
                                wire:click="confirmDelete('{{ $user->id }}')"
                                class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition-colors" 
                                title="Hapus"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </x-datatable.cell>
            </x-datatable.row>
        @endforeach
    </x-datatable>

    {{-- Form Modal (Create/Edit) --}}
    <x-modals.form-modal wire:model="showFormModal" :title="$editId ? 'Edit Pengguna' : 'Tambah Pengguna'" maxWidth="lg">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input 
                    label="Nama Lengkap" 
                    wire:model="name" 
                    placeholder="Masukkan nama lengkap" 
                    required 
                />
                <flux:input 
                    label="NIP" 
                    wire:model="nip" 
                    placeholder="Nomor Induk Pegawai" 
                />
            </div>
            
            <flux:input 
                label="Email" 
                type="email" 
                wire:model="email" 
                placeholder="contoh@email.com" 
                required 
            />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input 
                    label="{{ $editId ? 'Password Baru (opsional)' : 'Password' }}" 
                    type="password" 
                    wire:model="password" 
                    placeholder="{{ $editId ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                    :required="!$editId"
                />
                <flux:input 
                    label="Konfirmasi Password" 
                    type="password" 
                    wire:model="password_confirmation" 
                    placeholder="Ulangi password"
                    :required="!$editId"
                />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:select label="Role" wire:model="role" required>
                    @foreach($roles as $r)
                        <flux:select.option value="{{ $r }}">{{ $roleLabels[$r] ?? ucfirst($r) }}</flux:select.option>
                    @endforeach
                </flux:select>
                
                <div class="flex items-end pb-1">
                    <flux:checkbox 
                        label="Akun Aktif" 
                        wire:model="is_active"
                    />
                </div>
            </div>
            
            <div class="flex justify-end gap-2 pt-4">
                <flux:button variant="ghost" type="button" wire:click="closeFormModal">Batal</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editId ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                </flux:button>
            </div>
        </form>
    </x-modals.form-modal>

    {{-- Delete Confirmation Modal --}}
    <x-modals.delete-confirm 
        wire:model="showDeleteModal"
        title="Hapus Pengguna?"
        message="Apakah Anda yakin ingin menghapus :item? Tindakan ini tidak dapat dibatalkan."
        :itemName="$deleteName"
        confirmAction="delete"
        cancelAction="closeDeleteModal"
    />

    {{-- Reset Password Modal --}}
    <x-modals.form-modal wire:model="showResetPasswordModal" title="Reset Password" maxWidth="md">
        <div class="mb-4">
            <p class="text-sm text-zinc-600">
                Reset password untuk pengguna <strong>{{ $resetPasswordName }}</strong>
            </p>
        </div>
        
        <form wire:submit="resetPassword" class="space-y-4">
            <flux:input 
                label="Password Baru" 
                type="password" 
                wire:model="newPassword" 
                placeholder="Minimal 8 karakter" 
                required 
            />
            <flux:input 
                label="Konfirmasi Password Baru" 
                type="password" 
                wire:model="newPassword_confirmation" 
                placeholder="Ulangi password baru" 
                required 
            />
            
            <div class="flex justify-end gap-2 pt-4">
                <flux:button variant="ghost" type="button" wire:click="closeResetPasswordModal">Batal</flux:button>
                <flux:button type="submit" variant="primary">Reset Password</flux:button>
            </div>
        </form>
    </x-modals.form-modal>
</div>

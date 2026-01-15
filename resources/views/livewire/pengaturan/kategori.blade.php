<?php

use App\Models\Category;
use App\Models\Archive;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

new class extends Component {
    use WithPagination;
    
    #[Url]
    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'code';
    public string $sortDirection = 'asc';
    
    // Form
    public ?string $editId = null;
    public string $code = '';
    public string $name = '';
    public string $description = '';
    public int $retention_years = 5;
    
    // Modal
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;  
    public ?string $deleteId = null;
    public string $deleteName = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field 
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc') 
            : 'asc';
        $this->sortField = $field;
    }

    public function openCreateModal(): void
    {
        $this->reset(['editId', 'code', 'name', 'description']);
        $this->retention_years = 5;
        $this->showFormModal = true;
    }

    public function openEditModal(string $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editId = $id;
        $this->code = $cat->code;
        $this->name = $cat->name;
        $this->description = $cat->description ?? '';
        $this->retention_years = $cat->retention_years ?? 5;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'retention_years' => 'required|integer|min:1|max:100',
        ]);

        if ($this->editId) {
            Category::findOrFail($this->editId)->update($data);
            session()->flash('message', 'Kategori berhasil diperbarui.');
        } else {
            Category::create($data);
            session()->flash('message', 'Kategori berhasil ditambahkan.');
        }
        
        $this->showFormModal = false;
        $this->reset(['editId', 'code', 'name', 'description']);
        $this->retention_years = 5;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->reset(['editId', 'code', 'name', 'description']);
    }

    public function confirmDelete(string $id): void
    {
        $cat = Category::find($id);
        $this->deleteId = $id;
        $this->deleteName = $cat->name ?? '';
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            Category::findOrFail($this->deleteId)->delete();
            session()->flash('message', 'Kategori berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function with(): array
    {
        return [
            'categories' => Category::query()
                ->when($this->search, fn($q) => $q
                    ->where('code', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                )
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
        ];
    }

    public function archiveCount(string $id): int
    {
        return Archive::where('category_id', $id)->count();
    }
}; ?>

<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900">Kelola Kategori Arsip</h1>
                <p class="text-md text-zinc-500 mt-1">Manajemen klasifikasi dan kategori arsip digital</p>
            </div>
            <x-ui.button variant="success" wireClick="openCreateModal" wireTarget="openCreateModal" loadingText="Memuat...">
                <svg class="w-4 h-4 shrink-0" wire:loading.remove wire:target="openCreateModal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span wire:loading.remove wire:target="openCreateModal">Tambah Kategori</span>
            </x-ui.button>
        </div>
    </div>

    {{-- Flash --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700 text-sm">{{ session('message') }}</p>
        </div>
    @endif

    {{-- Table --}}
    <x-datatable 
        :items="$categories"
        searchPlaceholder="Cari kategori..."
        emptyMessage="Tidak ada data kategori"
        emptyAction="openCreateModal"
        emptyActionText="Tambah kategori baru"
    >
        <x-slot:columns>
            <x-datatable.th w="60">No</x-datatable.th>
            <x-datatable.th sortable="code" :$sortField :$sortDirection>Kode - Nama Kategori</x-datatable.th>
            <x-datatable.th sortable="retention_years" :$sortField :$sortDirection center>Masa Simpan</x-datatable.th>
            <x-datatable.th center>Jumlah Arsip</x-datatable.th>
            <x-datatable.th center w="120">Aksi</x-datatable.th>
        </x-slot:columns>

        @foreach ($categories as $i => $cat)
            <x-datatable.row>
                <x-datatable.cell>
                    <span class="font-medium text-blue-600">{{ $categories->firstItem() + $i }}</span>
                </x-datatable.cell>
                
                <x-datatable.cell wrap>
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-blue-100 text-blue-700 font-bold text-sm flex-shrink-0">
                            {{ $cat->code }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-zinc-900">{{ $cat->name }}</p>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ $cat->description ?? '-' }}</p>
                        </div>
                    </div>
                </x-datatable.cell>
                
                <x-datatable.cell center>
                    @php
                        $years = $cat->retention_years ?? 0;
                        $color = match(true) { $years >= 25 => 'purple', $years >= 10 => 'green', $years >= 5 => 'blue', default => 'zinc' };
                    @endphp
                    <x-datatable.badge :color="$color" icon="calendar">
                        {{ $years >= 99 ? 'Permanen' : $years.' Tahun' }}
                    </x-datatable.badge>
                </x-datatable.cell>
                
                <x-datatable.cell center>
                    <x-datatable.badge color="amber">{{ $this->archiveCount($cat->id) }} Arsip</x-datatable.badge>
                </x-datatable.cell>
                
                <x-datatable.cell center>
                    <x-datatable.actions 
                        edit="openEditModal('{{ $cat->id }}')" 
                        delete="confirmDelete('{{ $cat->id }}')" 
                    />
                </x-datatable.cell>
            </x-datatable.row>
        @endforeach
    </x-datatable>

    {{-- Form Modal --}}
    <x-modals.form-modal wire:model="showFormModal" :title="$editId ? 'Edit Kategori' : 'Tambah Kategori'" maxWidth="md">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Kode Kategori <span class="text-red-500">*</span></label>
                <input type="text" wire:model="code" placeholder="Contoh: 470" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" placeholder="Contoh: Kependudukan" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Deskripsi</label>
                <textarea wire:model="description" placeholder="Deskripsi singkat..." rows="2" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Masa Simpan (tahun) <span class="text-red-500">*</span></label>
                <input type="number" wire:model="retention_years" min="1" max="100" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-white text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('retention_years') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="flex justify-end gap-2 pt-4">
                <x-ui.button type="button" variant="secondary" wireClick="closeFormModal">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" wireTarget="save" loadingText="Menyimpan...">
                    {{ $editId ? 'Simpan' : 'Tambah' }}
                </x-ui.button>
            </div>
        </form>
    </x-modals.form-modal>

    {{-- Delete Modal --}}
    <x-modals.delete-confirm 
        wire:model="showDeleteModal"
        title="Hapus Kategori?"
        message="Apakah Anda yakin ingin menghapus :item?"
        :itemName="$deleteName"
        confirmAction="delete"
        cancelAction="closeDeleteModal"
    />
</div>
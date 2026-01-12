<?php

use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'code';
    public string $sortDirection = 'asc';

    // Form fields
    public ?string $editId = null;
    public string $code = '';
    public string $name = '';
    public int $retention_years = 5;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?string $deleteId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['editId', 'code', 'name', 'retention_years']);
        $this->retention_years = 5;
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $category = Category::findOrFail($id);
        $this->editId = $id;
        $this->code = $category->code;
        $this->name = $category->name;
        $this->retention_years = $category->retention_years ?? 5;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:100',
            'retention_years' => 'required|integer|min:1|max:100',
        ]);

        if ($this->editId) {
            $category = Category::findOrFail($this->editId);
            $category->update($validated);
            session()->flash('message', 'Kategori berhasil diperbarui.');
        } else {
            Category::create($validated);
            session()->flash('message', 'Kategori berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->reset(['editId', 'code', 'name', 'retention_years']);
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
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

    public function with(): array
    {
        $query = Category::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%');
            });
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return [
            'categories' => $query->paginate($this->perPage),
        ];
    }
}; ?>

<x-layouts.app.sidebar>
    <div class="p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <flux:heading size="xl">Kelola Kategori Arsip</flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400">Manajemen klasifikasi arsip digital</flux:text>
            </div>
            <flux:button icon="plus" variant="primary" wire:click="openCreateModal">
                Tambah Kategori
            </flux:button>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <flux:callout variant="success" class="mb-4">
                {{ session('message') }}
            </flux:callout>
        @endif

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
                icon="search" 
                placeholder="Cari kategori..." 
                wire:model.live.debounce.300ms="search" 
                class="w-64"
            />
        </div>

        {{-- Table --}}
        <flux:table :paginate="$categories">
            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'code'" :direction="$sortDirection" wire:click="sortBy('code')">
                    Kode
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortDirection" wire:click="sortBy('name')">
                    Nama Kategori
                </flux:table.column>
                <flux:table.column>Masa Simpan</flux:table.column>
                <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($categories as $index => $category)
                    <flux:table.row>
                        <flux:table.cell>{{ $categories->firstItem() + $index }}</flux:table.cell>
                        <flux:table.cell variant="strong">{{ $category->code }}</flux:table.cell>
                        <flux:table.cell>{{ $category->name }}</flux:table.cell>
                        <flux:table.cell>{{ $category->retention_years }} tahun</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button icon="pencil" size="sm" variant="ghost" wire:click="openEditModal('{{ $category->_id }}')" />
                                <flux:button icon="trash" size="sm" variant="ghost" class="text-red-500 hover:text-red-700" wire:click="confirmDelete('{{ $category->_id }}')" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">
                            Tidak ada data kategori.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Modal Form --}}
        <flux:modal wire:model="showModal" class="max-w-md">
            <div class="space-y-4">
                <flux:heading size="lg">{{ $editId ? 'Edit Kategori' : 'Tambah Kategori' }}</flux:heading>
                
                <form wire:submit="save" class="space-y-4">
                    <flux:input 
                        label="Kode Kategori" 
                        wire:model="code" 
                        placeholder="Contoh: 470"
                        required
                    />
                    <flux:input 
                        label="Nama Kategori" 
                        wire:model="name" 
                        placeholder="Contoh: Kependudukan"
                        required
                    />
                    <flux:input 
                        label="Masa Simpan (tahun)" 
                        wire:model="retention_years" 
                        type="number"
                        min="1"
                        max="100"
                        required
                    />
                    
                    <div class="flex justify-end gap-2 pt-4">
                        <flux:button variant="ghost" wire:click="$set('showModal', false)">Batal</flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $editId ? 'Simpan Perubahan' : 'Tambah' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Modal Confirm Delete --}}
        <flux:modal wire:model="showDeleteModal" class="max-w-sm">
            <div class="space-y-4 text-center">
                <flux:icon name="exclamation-triangle" class="w-12 h-12 text-red-500 mx-auto" />
                <flux:heading size="lg">Hapus Kategori?</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.</flux:text>
                
                <div class="flex justify-center gap-2 pt-4">
                    <flux:button variant="ghost" wire:click="$set('showDeleteModal', false)">Batal</flux:button>
                    <flux:button variant="danger" wire:click="delete">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</x-layouts.app.sidebar>

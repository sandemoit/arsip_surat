<?php

use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    // Form fields
    public ?string $editId = null;
    public string $code = '';
    public string $name = '';
    public int $retention_years = 5;
    
    // Modal states
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?string $deleteId = null;
    public string $deleteName = '';

    public function openCreateModal(): void
    {
        $this->reset(['editId', 'code', 'name']);
        $this->retention_years = 5;
        $this->showFormModal = true;
    }

    #[On('openEditModal')]
    public function openEditModal(string $id): void
    {
        $category = Category::findOrFail($id);
        $this->editId = $id;
        $this->code = $category->code;
        $this->name = $category->name;
        $this->retention_years = $category->retention_years ?? 5;
        $this->showFormModal = true;
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
        
        $this->showFormModal = false;
        $this->reset(['editId', 'code', 'name']);
        $this->retention_years = 5;
        $this->dispatch('refreshDatatable');
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->reset(['editId', 'code', 'name']);
        $this->retention_years = 5;
    }

    #[On('confirmDelete')]
    public function confirmDelete(string $id): void
    {
        $category = Category::find($id);
        $this->deleteId = $id;
        $this->deleteName = $category->name ?? '';
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
        $this->deleteName = '';
        $this->dispatch('refreshDatatable');
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteName = '';
    }
}; ?>

<div class="">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading class="text-slate-900 font-bold" size="xl">Kelola Kategori Arsip</flux:heading>
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

    {{-- Rappasoft DataTable --}}
    <x-card-body>
        <livewire:pengaturan.categories-table />
    </x-card-body>

    {{-- Form Modal (Add/Edit) --}}
    <x-modals.form-modal 
        wire:model="showFormModal" 
        :title="$editId ? 'Edit Kategori' : 'Tambah Kategori'"
        maxWidth="md"
    >
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
                <flux:button variant="ghost" type="button" wire:click="closeFormModal">Batal</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editId ? 'Simpan Perubahan' : 'Tambah' }}
                </flux:button>
            </div>
        </form>
    </x-modals.form-modal>

    {{-- Delete Confirmation Modal --}}
    <x-modals.delete-confirm 
        wire:model="showDeleteModal"
        title="Hapus Kategori?"
        message="Apakah Anda yakin ingin menghapus :item? Tindakan ini tidak dapat dibatalkan."
        :itemName="$deleteName"
        confirmAction="delete"
        cancelAction="closeDeleteModal"
    />
</div>
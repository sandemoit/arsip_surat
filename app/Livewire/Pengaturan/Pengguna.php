<?php

namespace App\Livewire\Pengaturan;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Pengguna extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('nip', 'like', '%'.$this->search.'%');
            });
        }

        $query->orderBy('name', 'asc');

        return view('livewire.pengaturan.pengguna', [
            'users' => $query->paginate($this->perPage),
        ]);
    }
}

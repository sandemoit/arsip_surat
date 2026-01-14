<?php

namespace App\Livewire\Pengaturan;

use App\Models\User;
use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Pengguna extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public int $perPage = 10;

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    // Form properties
    public ?string $editId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $nip = '';

    public string $role = 'staf';

    public bool $is_active = true;

    // Modal states
    public bool $showFormModal = false;

    public bool $showDeleteModal = false;

    public bool $showResetPasswordModal = false;

    public ?string $deleteId = null;

    public string $deleteName = '';

    public ?string $resetPasswordId = null;

    public string $resetPasswordName = '';

    public string $newPassword = '';

    public string $newPassword_confirmation = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';
        $this->sortField = $field;
    }

    public function openCreateModal(): void
    {
        $this->reset(['editId', 'name', 'email', 'password', 'password_confirmation', 'nip']);
        $this->role = RoleService::getDefaultRole();
        $this->is_active = true;
        $this->showFormModal = true;
    }

    public function openEditModal(string $id): void
    {
        $user = User::findOrFail($id);
        $this->editId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->nip = $user->nip ?? '';
        $this->role = $user->role ?? RoleService::getDefaultRole();
        $this->is_active = $user->is_active ?? true;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editId, '_id')],
            'nip' => 'nullable|string|max:30',
            'role' => ['required', Rule::in(RoleService::ROLES)],
            'is_active' => 'boolean',
        ];

        if (! $this->editId) {
            // Create: password wajib
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            // Edit: password opsional
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $validated = $this->validate($rules);

        if ($this->editId) {
            $user = User::findOrFail($this->editId);

            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'nip' => $validated['nip'],
                'role' => $validated['role'],
                'is_active' => $validated['is_active'],
            ];

            // Update password jika diisi
            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);
            session()->flash('message', 'Pengguna berhasil diperbarui.');
        } else {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'nip' => $validated['nip'],
                'role' => $validated['role'],
                'is_active' => $validated['is_active'],
            ]);
            session()->flash('message', 'Pengguna berhasil ditambahkan.');
        }

        $this->showFormModal = false;
        $this->reset(['editId', 'name', 'email', 'password', 'password_confirmation', 'nip']);
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->reset(['editId', 'name', 'email', 'password', 'password_confirmation', 'nip']);
        $this->resetValidation();
    }

    public function confirmDelete(string $id): void
    {
        // Tidak bisa hapus diri sendiri
        if ($id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');

            return;
        }

        $user = User::find($id);
        $this->deleteId = $id;
        $this->deleteName = $user->name ?? '';
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId && $this->deleteId !== Auth::id()) {
            User::findOrFail($this->deleteId)->delete();
            session()->flash('message', 'Pengguna berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function openResetPasswordModal(string $id): void
    {
        $user = User::findOrFail($id);
        $this->resetPasswordId = $id;
        $this->resetPasswordName = $user->name;
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
        $this->showResetPasswordModal = true;
    }

    public function resetPassword(): void
    {
        $this->validate([
            'newPassword' => 'required|string|min:8|confirmed',
        ]);

        if ($this->resetPasswordId) {
            $user = User::findOrFail($this->resetPasswordId);
            $user->update([
                'password' => Hash::make($this->newPassword),
            ]);
            session()->flash('message', 'Password berhasil direset untuk '.$this->resetPasswordName.'.');
        }

        $this->showResetPasswordModal = false;
        $this->resetPasswordId = null;
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
    }

    public function closeResetPasswordModal(): void
    {
        $this->showResetPasswordModal = false;
        $this->resetPasswordId = null;
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
        $this->resetValidation();
    }

    public function toggleActive(string $id): void
    {
        // Tidak bisa nonaktifkan diri sendiri
        if ($id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');

            return;
        }

        $user = User::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Pengguna {$user->name} berhasil {$status}.");
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

        $query->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.pengaturan.pengguna', [
            'users' => $query->paginate($this->perPage),
            'roles' => RoleService::ROLES,
            'roleLabels' => RoleService::ROLE_LABELS,
        ]);
    }
}

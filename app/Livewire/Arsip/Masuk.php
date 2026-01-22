<?php

namespace App\Livewire\Arsip;

use App\Exports\ArsipExport;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Disposition;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Masuk extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public int $perPage = 10;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public string $filterCategory = '';

    // Modal states
    public bool $showViewModal = false;

    public bool $showDeleteModal = false;

    public bool $showDisposisiModal = false;

    public ?string $viewId = null;

    public ?string $deleteId = null;

    public string $deleteName = '';

    public ?array $viewArchive = null;

    // Disposisi form
    public ?string $disposisiArchiveId = null;

    public string $disposisiArchiveName = '';

    public string $disposisiReceiverId = '';

    public string $disposisiInstruction = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'desc';
        $this->sortField = $field;
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('code')->get();
    }

    #[Computed]
    public function users()
    {
        return User::where('_id', '!=', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function openViewModal(string $id): void
    {
        $archive = Archive::with(['category', 'uploader'])->find($id);
        if ($archive) {
            $this->viewId = $id;
            $this->viewArchive = [
                'id' => $archive->id,
                'nomor_surat' => $archive->main_meta['nomor_surat'] ?? '-',
                'tanggal' => $archive->main_meta['tanggal'] ?? '-',
                'perihal' => $archive->main_meta['perihal'] ?? '-',
                'pengirim' => $archive->main_meta['pengirim'] ?? '-',
                'penerima' => $archive->main_meta['penerima'] ?? '-',
                'keterangan' => $archive->main_meta['keterangan'] ?? '-',
                'kategori' => $archive->category->name ?? '-',
                'uploader' => $archive->uploader->name ?? '-',
                'file_name' => $archive->file_info['original_name'] ?? '-',
                'file_size' => isset($archive->file_info['size']) ? number_format($archive->file_info['size'] / 1024 / 1024, 2) . ' MB' : '-',
                'file_type' => $archive->file_info['type'] ?? '-',
                'file_path' => $archive->file_info['path'] ?? null,
                'created_at' => $archive->created_at?->format('d M Y, H:i') ?? '-',
            ];
            $this->showViewModal = true;
        }
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewId = null;
        $this->viewArchive = null;
    }

    public function downloadFile(): void
    {
        if ($this->viewArchive && isset($this->viewArchive['file_path'])) {
            $path = $this->viewArchive['file_path'];
            if (Storage::exists($path)) {
                $this->dispatch('download-file', url: Storage::url($path));
            }
        }
    }

    public function confirmDelete(string $id): void
    {
        $archive = Archive::find($id);
        if ($archive) {
            $this->deleteId = $id;
            $this->deleteName = $archive->main_meta['perihal'] ?? 'Arsip ini';
            $this->showDeleteModal = true;
        }
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $archive = Archive::find($this->deleteId);
            if ($archive) {
                if (isset($archive->file_info['path']) && Storage::exists($archive->file_info['path'])) {
                    Storage::delete($archive->file_info['path']);
                }
                $archive->delete();
                session()->flash('message', 'Arsip berhasil dihapus.');
            }
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function export()
    {
        $filename = 'arsip-surat-masuk-' . now()->format('Y-m-d-His') . '.xlsx';
        return Excel::download(new ArsipExport('masuk', $this->filterCategory, $this->search), $filename);
    }

    // Disposisi methods
    public function openDisposisiModal(string $id): void
    {
        $archive = Archive::find($id);
        if ($archive) {
            $this->disposisiArchiveId = $id;
            $this->disposisiArchiveName = $archive->main_meta['perihal'] ?? 'Arsip';
            $this->disposisiReceiverId = '';
            $this->disposisiInstruction = '';
            $this->showDisposisiModal = true;
        }
    }

    public function closeDisposisiModal(): void
    {
        $this->showDisposisiModal = false;
        $this->disposisiArchiveId = null;
        $this->disposisiArchiveName = '';
        $this->disposisiReceiverId = '';
        $this->disposisiInstruction = '';
        $this->resetValidation();
    }

    public function sendDisposisi(): void
    {
        $this->validate([
            'disposisiReceiverId' => 'required|exists:users,_id',
            'disposisiInstruction' => 'required|string|min:10|max:1000',
        ], [
            'disposisiReceiverId.required' => 'Pilih penerima disposisi.',
            'disposisiReceiverId.exists' => 'Penerima tidak valid.',
            'disposisiInstruction.required' => 'Instruksi disposisi wajib diisi.',
            'disposisiInstruction.min' => 'Instruksi minimal 10 karakter.',
            'disposisiInstruction.max' => 'Instruksi maksimal 1000 karakter.',
        ]);

        Disposition::create([
            'archive_id' => $this->disposisiArchiveId,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->disposisiReceiverId,
            'instruction' => $this->disposisiInstruction,
            'status' => Disposition::STATUS_PENDING,
        ]);

        $this->closeDisposisiModal();
        session()->flash('message', 'Disposisi berhasil dikirim.');
    }

    public function getFileIcon(string $type): array
    {
        return match (true) {
            str_contains($type, 'pdf') => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'icon' => 'document'],
            str_contains($type, 'word') || str_contains($type, 'doc') => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'document-text'],
            str_contains($type, 'image') => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'photo'],
            default => ['bg' => 'bg-zinc-100', 'text' => 'text-zinc-600', 'icon' => 'document'],
        };
    }

    public function render()
    {
        $query = Archive::with(['category', 'uploader'])
            ->byJenis('masuk');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('main_meta.nomor_surat', 'like', '%' . $this->search . '%')
                    ->orWhere('main_meta.perihal', 'like', '%' . $this->search . '%')
                    ->orWhere('main_meta.pengirim', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.arsip.masuk', [
            'archives' => $query->paginate($this->perPage),
        ]);
    }
}

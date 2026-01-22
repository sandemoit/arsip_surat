<?php

namespace App\Livewire\Disposisi;

use App\Exports\DisposisiExport;
use App\Models\Disposition;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Riwayat extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public int $perPage = 10;

    // Modal states
    public bool $showViewModal = false;

    public ?array $viewDisposition = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function openViewModal(string $id): void
    {
        $disposition = Disposition::with(['archive', 'sender', 'receiver', 'archive.category'])->find($id);
        if ($disposition) {
            $this->viewDisposition = [
                'id' => $disposition->id,
                'instruction' => $disposition->instruction,
                'status' => $disposition->status,
                'sender_name' => $disposition->sender->name ?? '-',
                'receiver_name' => $disposition->receiver->name ?? '-',
                'created_at' => $disposition->created_at?->format('d M Y, H:i') ?? '-',
                'updated_at' => $disposition->updated_at?->format('d M Y, H:i') ?? '-',
                'archive' => $disposition->archive ? [
                    'perihal' => $disposition->archive->main_meta['perihal'] ?? '-',
                    'nomor_surat' => $disposition->archive->main_meta['nomor_surat'] ?? '-',
                    'tanggal' => $disposition->archive->main_meta['tanggal'] ?? '-',
                    'kategori' => $disposition->archive->category->name ?? '-',
                    'file_path' => $disposition->archive->file_info['path'] ?? null,
                ] : null,
            ];
            $this->showViewModal = true;
        }
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewDisposition = null;
    }

    public function export()
    {
        $filename = 'riwayat-disposisi-' . now()->format('Y-m-d-His') . '.xlsx';
        return Excel::download(new DisposisiExport, $filename);
    }

    public function render()
    {
        $userId = Auth::id();

        // Query: semua disposisi yang related dengan user (sebagai sender ATAU receiver)
        $query = Disposition::with(['archive', 'sender', 'receiver', 'archive.category'])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('instruction', 'like', '%' . $this->search . '%')
                    ->orWhereHas('archive', function ($aq) {
                        $aq->where('main_meta.perihal', 'like', '%' . $this->search . '%')
                            ->orWhere('main_meta.nomor_surat', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $query->orderBy('created_at', 'desc');

        return view('livewire.disposisi.riwayat', [
            'dispositions' => $query->paginate($this->perPage),
        ]);
    }
}

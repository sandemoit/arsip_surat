<?php

namespace App\Livewire\Disposisi;

use App\Models\Disposition;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Masuk extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public string $filter = 'all'; // all, pending

    public int $perPage = 10;

    // Modal states
    public bool $showViewModal = false;

    public ?array $viewDisposition = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function markAsSelesai(string $id): void
    {
        $disposition = Disposition::where('receiver_id', Auth::id())->find($id);
        if ($disposition) {
            $disposition->markAsSelesai();
            session()->flash('message', 'Disposisi telah ditandai selesai.');
        }
    }

    public function openViewModal(string $id): void
    {
        $disposition = Disposition::with(['archive', 'sender', 'archive.category'])->find($id);
        if ($disposition) {
            $this->viewDisposition = [
                'id' => $disposition->id,
                'instruction' => $disposition->instruction,
                'status' => $disposition->status,
                'sender_name' => $disposition->sender->name ?? '-',
                'created_at' => $disposition->created_at?->format('d M Y, H:i') ?? '-',
                'archive' => $disposition->archive ? [
                    'id' => $disposition->archive->id,
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

    #[Computed]
    public function stats()
    {
        $userId = Auth::id();

        return [
            'pending' => Disposition::where('receiver_id', $userId)->pending()->count(),
            'selesai_today' => Disposition::where('receiver_id', $userId)
                ->selesai()
                ->whereDate('updated_at', today())
                ->count(),
            'total' => Disposition::where('receiver_id', $userId)->count(),
        ];
    }

    public function render()
    {
        $query = Disposition::with(['archive', 'sender', 'archive.category'])
            ->where('receiver_id', Auth::id());

        if ($this->filter === 'pending') {
            $query->pending();
        }

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

        return view('livewire.disposisi.masuk', [
            'dispositions' => $query->paginate($this->perPage),
        ]);
    }
}

<?php

namespace App\Livewire\Disposisi;

use App\Models\Disposition;
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
    public bool $showSelesaiModal = false;

    public ?array $viewDisposition = null;

    // Form states untuk Selesai
    public ?string $selesaiId = null;
    public string $selesaiNotes = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    // ========================================
    // VIEW MODAL
    // ========================================

    public function openViewModal(string $id): void
    {
        $disposition = Disposition::with(['archive', 'sender', 'archive.category', 'parent', 'parent.sender'])->find($id);
        if ($disposition) {
            $this->viewDisposition = [
                'id' => $disposition->id,
                'instruction' => $disposition->instruction,
                'status' => $disposition->status,
                'status_badge' => $disposition->statusBadge,
                'sender_name' => $disposition->sender->name ?? '-',
                'created_at' => $disposition->created_at?->format('d M Y, H:i') ?? '-',
                'is_pending' => $disposition->isPending(),
                'parent' => $disposition->parent ? [
                    'sender_name' => $disposition->parent->sender->name ?? '-',
                    'instruction' => $disposition->parent->instruction,
                ] : null,
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

    // ========================================
    // SELESAI MODAL
    // ========================================

    public function openSelesaiModal(string $id): void
    {
        $disposition = Disposition::where('receiver_id', Auth::id())->pending()->find($id);
        if ($disposition) {
            $this->selesaiId = $id;
            $this->selesaiNotes = '';
            $this->showSelesaiModal = true;
        }
    }

    public function closeSelesaiModal(): void
    {
        $this->showSelesaiModal = false;
        $this->selesaiId = null;
        $this->selesaiNotes = '';
    }

    public function selesaikan(): void
    {
        if (!$this->selesaiId) return;

        $disposition = Disposition::where('receiver_id', Auth::id())->pending()->find($this->selesaiId);
        if ($disposition) {
            $disposition->markAsSelesai($this->selesaiNotes ?: null);
            session()->flash('message', 'Disposisi berhasil disetujui.');
        }

        $this->closeSelesaiModal();
    }

    // ========================================
    // STATS & RENDER
    // ========================================

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

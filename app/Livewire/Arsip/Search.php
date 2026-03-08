<?php

namespace App\Livewire\Arsip;

use App\Models\Archive;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public string $jenisSurat = '';

    public string $categoryId = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public bool $showFilters = true;

    public int $perPage = 10;

    // Modal states
    public bool $showViewModal = false;

    public ?array $viewArchive = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingJenisSurat(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'jenisSurat', 'categoryId', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('kode')->get();
    }

    public function openViewModal(string $id): void
    {
        $archive = Archive::with(['category', 'uploader'])->find($id);
        if ($archive) {
            $this->viewArchive = [
                'id' => $archive->id,
                'nomor_surat' => $archive->main_meta['nomor_surat'] ?? '-',
                'tanggal' => $archive->main_meta['tanggal'] ?? '-',
                'perihal' => $archive->main_meta['perihal'] ?? '-',
                'pengirim' => $archive->main_meta['pengirim'] ?? '-',
                'penerima' => $archive->main_meta['penerima'] ?? '-',
                'ringkasan' => $archive->main_meta['ringkasan']
                    ?? $archive->main_meta['keterangan']
                    ?? '-',
                'kategori' => $archive->category->name ?? '-',
                'uploader' => $archive->uploader->name ?? '-',
                'jenis_surat' => $archive->jenis_surat ?? '-',
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
        $this->viewArchive = null;
    }

    public function render()
    {
        $query = Archive::with(['category', 'uploader']);

        // Search keyword
        if (trim($this->search) !== '') {
            $keyword = preg_quote(trim($this->search), '/');

            $query->whereRaw([
                '$or' => [
                    ['main_meta.nomor_surat' => ['$regex' => $keyword, '$options' => 'i']],
                    ['main_meta.perihal' => ['$regex' => $keyword, '$options' => 'i']],
                    ['main_meta.ringkasan' => ['$regex' => $keyword, '$options' => 'i']],
                    ['main_meta.keterangan' => ['$regex' => $keyword, '$options' => 'i']], // fallback data lama
                    ['main_meta.pengirim' => ['$regex' => $keyword, '$options' => 'i']],
                    ['main_meta.penerima' => ['$regex' => $keyword, '$options' => 'i']],
                    ['ocr_text' => ['$regex' => $keyword, '$options' => 'i']],
                    ['file_info.original_name' => ['$regex' => $keyword, '$options' => 'i']],
                    [
                        '$expr' => [
                            '$anyElementTrue' => [
                                '$map' => [
                                    'input' => [
                                        '$objectToArray' => [
                                            '$cond' => [
                                                [
                                                    '$eq' => [
                                                        ['$type' => '$main_meta'],
                                                        'object',
                                                    ],
                                                ],
                                                '$main_meta',
                                                (object) [],
                                            ],
                                        ],
                                    ],
                                    'as' => 'item',
                                    'in' => [
                                        '$regexMatch' => [
                                            'input' => [
                                                '$convert' => [
                                                    'input' => '$$item.v',
                                                    'to' => 'string',
                                                    'onError' => '',
                                                    'onNull' => '',
                                                ],
                                            ],
                                            'regex' => $keyword,
                                            'options' => 'i',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        '$expr' => [
                            '$anyElementTrue' => [
                                '$map' => [
                                    'input' => [
                                        '$objectToArray' => [
                                            '$cond' => [
                                                [
                                                    '$eq' => [
                                                        ['$type' => '$dynamic_meta'],
                                                        'object',
                                                    ],
                                                ],
                                                '$dynamic_meta',
                                                (object) [],
                                            ],
                                        ],
                                    ],
                                    'as' => 'item',
                                    'in' => [
                                        '$regexMatch' => [
                                            'input' => [
                                                '$convert' => [
                                                    'input' => '$$item.v',
                                                    'to' => 'string',
                                                    'onError' => '',
                                                    'onNull' => '',
                                                ],
                                            ],
                                            'regex' => $keyword,
                                            'options' => 'i',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        }

        // Filter by jenis surat
        if ($this->jenisSurat) {
            $query->where('jenis_surat', $this->jenisSurat);
        }

        // Filter by category
        if ($this->categoryId) {
            $query->where('kategori_id', $this->categoryId);
        }

        // Filter by date range
        if ($this->dateFrom) {
            $query->where('main_meta.tanggal', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->where('main_meta.tanggal', '<=', $this->dateTo);
        }

        $query->orderBy('created_at', 'desc');

        return view('livewire.arsip.search', [
            'archives' => $query->paginate($this->perPage),
            'totalCount' => Archive::count(),
        ]);
    }
}

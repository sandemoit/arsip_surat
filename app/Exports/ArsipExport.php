<?php

namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArsipExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected string $jenisSurat;

    protected ?string $categoryId;

    protected string $search;

    public function __construct(string $jenisSurat, ?string $categoryId = null, string $search = '')
    {
        $this->jenisSurat = $jenisSurat;
        $this->categoryId = $categoryId;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Archive::with(['category', 'uploader']);

        // Filter by jenis surat
        if ($this->jenisSurat === 'lainnya') {
            $query->where('jenis_surat', 'lainnya');
        } else {
            $query->where('jenis_surat', $this->jenisSurat);
        }

        // Filter by category
        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('main_meta.perihal', 'like', '%'.$this->search.'%')
                    ->orWhere('main_meta.nomor_surat', 'like', '%'.$this->search.'%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        $baseHeadings = [
            'No',
            'Nomor Surat',
            'Perihal',
            'Tanggal',
        ];

        if ($this->jenisSurat === 'masuk') {
            $baseHeadings[] = 'Pengirim';
        } else {
            $baseHeadings[] = 'Penerima';
        }

        return array_merge($baseHeadings, [
            'Kategori',
            'Diupload Oleh',
            'Tanggal Upload',
        ]);
    }

    public function map($archive): array
    {
        static $no = 0;
        $no++;

        $baseData = [
            $no,
            $archive->main_meta['nomor_surat'] ?? '-',
            $archive->main_meta['perihal'] ?? '-',
            $archive->main_meta['tanggal'] ?? '-',
        ];

        if ($this->jenisSurat === 'masuk') {
            $baseData[] = $archive->main_meta['pengirim'] ?? '-';
        } else {
            $baseData[] = $archive->main_meta['penerima'] ?? '-';
        }

        return array_merge($baseData, [
            $archive->category->name ?? '-',
            $archive->uploader->name ?? '-',
            $archive->created_at?->format('d M Y, H:i'),
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ];
    }
}

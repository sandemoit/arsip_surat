<?php

namespace App\Exports;

use App\Models\Disposition;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DisposisiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        $userId = Auth::id();

        return Disposition::with(['archive', 'sender', 'receiver', 'archive.category'])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Dokumen',
            'Nomor Surat',
            'Tanggal',
            'Dari',
            'Kepada',
            'Arah',
            'Status',
            'Instruksi',
        ];
    }

    public function map($disposition): array
    {
        static $no = 0;
        $no++;

        $isSent = $disposition->sender_id === Auth::id();

        return [
            $no,
            $disposition->archive->main_meta['perihal'] ?? '-',
            $disposition->archive->main_meta['nomor_surat'] ?? '-',
            $disposition->created_at?->format('d M Y, H:i'),
            $disposition->sender->name ?? '-',
            $disposition->receiver->name ?? '-',
            $isSent ? 'Keluar' : 'Masuk',
            ucfirst($disposition->status),
            $disposition->instruction,
        ];
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

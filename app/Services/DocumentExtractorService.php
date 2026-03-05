<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class DocumentExtractorService
{
    private const BULAN_MAP = [
        'januari' => '01', 'februari' => '02', 'maret' => '03',
        'april' => '04', 'mei' => '05', 'juni' => '06',
        'juli' => '07', 'agustus' => '08', 'september' => '09',
        'oktober' => '10', 'november' => '11', 'desember' => '12',
        'nopember' => '11', // variasi ejaan lama
    ];

    /**
     * Extract nomor surat dan tanggal surat dari file.
     *
     * @return array{nomor_surat: ?string, tanggal_surat: ?string}
     */
    public function extract(string $filePath, string $extension): array
    {
        $text = match (strtolower($extension)) {
            'pdf' => $this->extractTextFromPdf($filePath),
            'docx' => $this->extractTextFromDocx($filePath),
            'doc' => $this->extractTextFromDoc($filePath),
            default => '',
        };

        if (empty(trim($text))) {
            return ['nomor_surat' => null, 'tanggal_surat' => null, 'perihal' => null];
        }

        // Cek kualitas teks — jika garbled (font encoding rusak), jangan proses
        if (!$this->isTextReadable($text)) {
            return ['nomor_surat' => null, 'tanggal_surat' => null, 'perihal' => null];
        }

        return [
            'nomor_surat' => $this->extractNomorSurat($text),
            'tanggal_surat' => $this->extractTanggalSurat($text),
            'perihal' => $this->extractPerihal($text),
        ];
    }

    /**
     * Cek apakah teks yang di-extract bisa dibaca (bukan garbled/rusak).
     * Caranya: cek apakah teks mengandung kata-kata umum Indonesia/surat.
     */
    private function isTextReadable(string $text): bool
    {
        $commonWords = [
            'nomor', 'tanggal', 'hal', 'perihal', 'kepada', 'dari',
            'surat', 'dengan', 'yang', 'untuk', 'dan', 'atau',
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember',
            'hormat', 'menteri', 'dinas', 'pemerintah', 'kabupaten', 'kota',
            'provinsi', 'bupati', 'camat', 'lurah', 'desa', 'kecamatan',
            'lampiran', 'tembusan', 'demikian', 'atas',
        ];

        $lowerText = strtolower($text);
        $matchCount = 0;

        foreach ($commonWords as $word) {
            if (str_contains($lowerText, $word)) {
                $matchCount++;
            }
            // Minimal 3 kata umum ditemukan = teks bisa dibaca
            if ($matchCount >= 3) {
                return true;
            }
        }

        return false;
    }

    private function extractTextFromPdf(string $filePath): string
    {
        // Primary: gunakan pdftotext (poppler-utils) — lebih akurat untuk font encoding
        $text = $this->extractPdfWithPdftotext($filePath);
        if (!empty(trim($text))) {
            return $text;
        }

        // Fallback: smalot/pdfparser (pure PHP, kurang akurat untuk custom fonts)
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);

            return $pdf->getText();
        } catch (\Throwable) {
            return '';
        }
    }

    private function extractPdfWithPdftotext(string $filePath): string
    {
        // pdftotext hanya tersedia di Linux/Mac
        if (PHP_OS_FAMILY === 'Windows') {
            return '';
        }

        try {
            $output = [];
            $exitCode = 0;
            exec('pdftotext ' . escapeshellarg($filePath) . ' - 2>/dev/null', $output, $exitCode);

            if ($exitCode === 0 && !empty($output)) {
                return implode("\n", $output);
            }
        } catch (\Throwable) {
            // pdftotext tidak tersedia
        }

        return '';
    }

    private function extractTextFromDocx(string $filePath): string
    {
        try {
            $phpWord = WordIOFactory::load($filePath, 'Word2007');

            return $this->getTextFromPhpWord($phpWord);
        } catch (\Throwable) {
            return '';
        }
    }

    private function extractTextFromDoc(string $filePath): string
    {
        // antiword hanya tersedia di Linux/Mac — skip di Windows
        if (PHP_OS_FAMILY === 'Windows') {
            return '';
        }

        try {
            $output = [];
            $exitCode = 0;
            exec('antiword ' . escapeshellarg($filePath) . ' 2>/dev/null', $output, $exitCode);

            if ($exitCode === 0 && !empty($output)) {
                return implode("\n", $output);
            }
        } catch (\Throwable) {
            // antiword tidak tersedia
        }

        return '';
    }

    private function getTextFromPhpWord(\PhpOffice\PhpWord\PhpWord $phpWord): string
    {
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text .= $this->extractElementText($element) . "\n";
            }
        }

        return $text;
    }

    private function extractElementText(mixed $element): string
    {
        // TextRun contains multiple Text elements
        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            $parts = [];
            foreach ($element->getElements() as $child) {
                $parts[] = $this->extractElementText($child);
            }

            return implode('', $parts);
        }

        // Simple Text element
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            return $element->getText() ?? '';
        }

        // Table → iterate rows → cells → elements
        if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            $parts = [];
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $cellElement) {
                        $parts[] = $this->extractElementText($cellElement);
                    }
                }
            }

            return implode(' ', $parts);
        }

        return '';
    }

    /**
     * Extract nomor surat dari teks.
     * Strategi bertingkat untuk berbagai format surat dan layout PDF.
     */
    private function extractNomorSurat(string $text): ?string
    {
        // Normalize: collapse spasi/tab ganda, tapi pertahankan newline
        $normalized = preg_replace('/[ \t]+/', ' ', $text);

        // Pre-process: fix reversed PDF layout
        // Pola PDF: ": 6356/IT2.IX.8/B/TU.00.09/VIII/2024\nNomor" → jadi "Nomor : 6356/..."
        // Juga berlaku untuk Perihal, Hal, Lampiran
        $normalized = preg_replace_callback(
            '/^[ ]*:[ ]*(.+)\n[ ]*(Nomor|No\.?|Perihal|Hal|Lampiran|Lampiram)[ ]*$/mu',
            fn ($m) => $m[2] . ' : ' . $m[1],
            $normalized
        );

        // Strategy 1: Label "Nomor" / "No." diikuti ":" — ambil SEMUA sampai akhir baris
        // Contoh: "Nomor : 6356/IT2.IX.8/B/TU.00.09/VIII/2024"
        if (preg_match('/(?:Nomor|No\.?)\s*[:]\s*([^\n\r]+)/iu', $normalized, $match)) {
            $value = $this->cleanNomorSurat(trim($match[1]));
            if ($value && strlen($value) >= 3 && strlen($value) <= 100) {
                return $value;
            }
        }

        // Strategy 2: Label "Nomor" tanpa ":" (format: "Nomor 005/KPTS/2024")
        if (preg_match('/(?:Nomor|No\.?)\s+([^\n\r]+)/iu', $normalized, $match)) {
            $value = $this->cleanNomorSurat(trim($match[1]));
            if ($value && strlen($value) >= 3 && strlen($value) <= 100) {
                return $value;
            }
        }

        // Strategy 3: Cari pola nomor surat berdiri sendiri (minimal 3 segmen dengan "/")
        // Contoh: "6356/IT2.IX.8/B/TU.00.09/VIII/2024"
        if (preg_match('/\b([A-Za-z0-9][\w.\-]*(?:\/[\w.\-]+){2,6})\b/', $normalized, $match)) {
            $value = trim($match[1]);
            if (strlen($value) >= 5 && strlen($value) <= 100 && preg_match('/\d/', $value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Bersihkan hasil capture nomor surat dari noise trailing.
     * Contoh: "123/MJS/10/2019 Medan, 09 Oktober 2019" → "123/MJS/10/2019"
     */
    private function cleanNomorSurat(string $raw): ?string
    {
        if (empty($raw)) {
            return null;
        }

        // Potong di pattern yang menandakan akhir nomor surat:
        // - nama kota diikuti koma (Medan, Surabaya, Jakarta, dll)
        // - spasi diikuti "Hal" atau "Perihal" atau "Lampiran"
        // - spasi diikuti tanggal (angka + bulan Indonesia)
        $raw = preg_replace('/\s+(?:Hal|Perihal|Lampiran|Sifat|Kepada)\s*[:].*/iu', '', $raw);

        // Potong di nama-kota + koma (contoh: "Medan, 09 Oktober")
        $cities = 'Medan|Jakarta|Surabaya|Bandung|Semarang|Makassar|Palembang|Denpasar|Yogyakarta|Malang|Bogor|Bekasi|Tangerang|Depok|Padang|Manado|Pontianak|Banjarmasin|Mataram|Kupang|Pekanbaru|Jambi|Bengkulu|Lampung|Kendari|Palu|Ambon|Jayapura|Gorontalo|Mamuju|Ternate|Pangkalpinang|Serang|Samarinda|Balikpapan';
        $raw = preg_replace('/\s*(?:' . $cities . ')\s*,.*/iu', '', $raw);

        // Potong di pola tanggal (angka + bulan Indonesia)
        $bulan = 'Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember|Nopember';
        $raw = preg_replace('/\s+\d{1,2}\s+(?:' . $bulan . ')\s+\d{4}.*/iu', '', $raw);

        $raw = trim($raw);

        return !empty($raw) ? $raw : null;
    }

    /**
     * Extract tanggal surat dari teks, return format Y-m-d.
     * Cari pola tanggal Indonesia (15 Januari 2024) atau angka (15/01/2024).
     */
    private function extractTanggalSurat(string $text): ?string
    {
        $bulanNames = implode('|', array_keys(self::BULAN_MAP));

        // Normalize whitespace (PDF parser sering menghasilkan multi-space)
        $normalized = preg_replace('/[ \t]+/', ' ', $text);

        // Strategy 1: Format Indonesia "15 Januari 2024" atau "Palembang, 15 Januari 2024"
        // Gunakan \s+ yang flexible untuk handle spasi ganda dari PDF
        if (preg_match('/(\d{1,2})\s+(' . $bulanNames . ')\s+(\d{4})/iu', $normalized, $match)) {
            $day = str_pad($match[1], 2, '0', STR_PAD_LEFT);
            $month = self::BULAN_MAP[strtolower($match[2])] ?? null;
            $year = $match[3];

            if ($month && checkdate((int) $month, (int) $day, (int) $year)) {
                return "{$year}-{$month}-{$day}";
            }
        }

        // Strategy 2: Label "Tanggal" diikuti format Indonesia
        if (preg_match('/(?:Tanggal|Tgl\.?)\s*[:]\s*(\d{1,2})\s+(' . $bulanNames . ')\s+(\d{4})/iu', $normalized, $match)) {
            $day = str_pad($match[1], 2, '0', STR_PAD_LEFT);
            $month = self::BULAN_MAP[strtolower($match[2])] ?? null;
            $year = $match[3];

            if ($month && checkdate((int) $month, (int) $day, (int) $year)) {
                return "{$year}-{$month}-{$day}";
            }
        }

        // Strategy 3: Format "tanggal DD bulan YYYY" dalam konteks kalimat
        // Contoh: "pada tanggal 05 Oktober 2019"
        if (preg_match('/tanggal\s+(\d{1,2})\s+(' . $bulanNames . ')\s+(\d{4})/iu', $normalized, $match)) {
            $day = str_pad($match[1], 2, '0', STR_PAD_LEFT);
            $month = self::BULAN_MAP[strtolower($match[2])] ?? null;
            $year = $match[3];

            if ($month && checkdate((int) $month, (int) $day, (int) $year)) {
                return "{$year}-{$month}-{$day}";
            }
        }

        // Strategy 4: Format angka DD/MM/YYYY atau DD-MM-YYYY
        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/', $normalized, $match)) {
            $day = str_pad($match[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($match[2], 2, '0', STR_PAD_LEFT);
            $year = $match[3];

            if (checkdate((int) $month, (int) $day, (int) $year)) {
                return "{$year}-{$month}-{$day}";
            }
        }

        // Strategy 5: Format "Bulan YYYY" tanpa hari (contoh: "Menanti, September 2025")
        // Default ke tanggal 1
        if (preg_match('/(' . $bulanNames . ')\s+(\d{4})/iu', $normalized, $match)) {
            $month = self::BULAN_MAP[strtolower($match[1])] ?? null;
            $year = $match[2];

            if ($month && checkdate((int) $month, 1, (int) $year)) {
                return "{$year}-{$month}-01";
            }
        }

        return null;
    }

    /**
     * Extract perihal/hal surat dari teks.
     * Contoh: "Hal : Pesanan Barang" atau "Perihal : Undangan Rapat"
     */
    private function extractPerihal(string $text): ?string
    {
        // Normalize whitespace
        $normalized = preg_replace('/[ \t]+/', ' ', $text);

        // Pre-process: fix reversed PDF layout untuk Perihal/Hal
        $normalized = preg_replace_callback(
            '/^[ ]*:[ ]*(.+)\n[ ]*(Perihal|Hal)[ ]*$/mu',
            fn ($m) => $m[2] . ' : ' . $m[1],
            $normalized
        );

        // Strategy 1: Label "Perihal" atau "Hal" diikuti ":" — ambil value
        if (preg_match('/(?:Perihal|Hal)\s*[:]\s*([^\n\r]+)/iu', $normalized, $match)) {
            $value = trim($match[1]);

            // Cek apakah ada lanjutan di baris berikutnya (perihal multi-baris)
            // Contoh: "Perihal : Penawaran "Workshop: Implementasi\n         Manajemen Perubahan""
            $pos = strpos($normalized, $match[0]);
            if ($pos !== false) {
                $after = substr($normalized, $pos + strlen($match[0]));
                $lines = explode("\n", $after);

                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    // Stop jika baris berikutnya adalah label lain atau kosong
                    if (empty($trimmed) || preg_match('/^(Nomor|No\.|Kepada|Lampiran|Lampiram|Sifat|Tanggal|Tgl)\s*[:]/iu', $trimmed)) {
                        break;
                    }
                    // Lanjutkan jika terlihat seperti lanjutan perihal (dimulai dengan huruf kecil atau kutip)
                    $value .= ' ' . $trimmed;
                }
            }

            // Bersihkan tanda kutip berlebih
            $value = trim($value, ' ""\'"');
            $value = trim($value);

            if (!empty($value) && strlen($value) >= 3) {
                return $value;
            }
        }

        return null;
    }
}

<?php

namespace App\Livewire\Arsip;

use App\Models\Archive;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    // File upload
    public $file;

    // Form fields
    public string $nomor_surat = '';

    public string $tanggal_surat = '';

    public string $perihal = '';

    public string $jenis_surat = '';

    public string $category_id = '';

    public string $pengirim = '';

    public string $penerima = '';

    public string $keterangan = '';

    // Jenis surat options
    public array $jenisOptions = [
        'masuk' => 'Surat Masuk',
        'keluar' => 'Surat Keluar',
        'sk' => 'SK / Keputusan',
        'lainnya' => 'Lainnya',
    ];

    protected function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'nomor_surat' => 'required|string|max:100',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string|max:255',
            'jenis_surat' => 'required|in:masuk,keluar,sk,lainnya',
            'category_id' => 'required|exists:categories,_id',
            'pengirim' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'file.required' => 'File dokumen wajib diupload.',
            'file.mimes' => 'Format file harus PDF, DOC, DOCX, JPG, atau PNG.',
            'file.max' => 'Ukuran file maksimal 10MB.',
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
            'perihal.required' => 'Perihal/judul surat wajib diisi.',
            'jenis_surat.required' => 'Jenis surat wajib dipilih.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
        ];
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('code')->get();
    }

    public function updatedFile()
    {
        $this->validateOnly('file');
    }

    public function removeFile(): void
    {
        $this->file = null;
        $this->resetValidation('file');
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Generate file path: archives/{year}/{month}/{random_name}.{ext}
        $year = date('Y');
        $month = date('m');
        $extension = $this->file->getClientOriginalExtension();
        $randomName = Str::random(40).'.'.$extension;
        $directory = "archives/{$year}/{$month}";
        $filePath = "{$directory}/{$randomName}";

        // Store file to public disk (storage/app/public/archives/...)
        $this->file->storeAs($directory, $randomName, 'public');

        // Create archive record
        Archive::create([
            'category_id' => $validated['category_id'],
            'uploader_id' => Auth::id(),
            'jenis_surat' => $validated['jenis_surat'],
            'main_meta' => [
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal' => $validated['tanggal_surat'],
                'perihal' => $validated['perihal'],
                'pengirim' => $validated['pengirim'] ?? null,
                'penerima' => $validated['penerima'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
            ],
            'file_info' => [
                'path' => $filePath,
                'original_name' => $this->file->getClientOriginalName(),
                'size' => $this->file->getSize(),
                'type' => $this->file->getMimeType(),
            ],
            'dynamic_meta' => [],
            'ocr_text' => null,
        ]);

        // Reset form
        $this->reset([
            'file',
            'nomor_surat',
            'tanggal_surat',
            'perihal',
            'jenis_surat',
            'category_id',
            'pengirim',
            'penerima',
            'keterangan',
        ]);

        session()->flash('message', 'Arsip berhasil diupload!');
    }

    public function cancel(): void
    {
        $this->reset([
            'file',
            'nomor_surat',
            'tanggal_surat',
            'perihal',
            'jenis_surat',
            'category_id',
            'pengirim',
            'penerima',
            'keterangan',
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.arsip.upload');
    }
}

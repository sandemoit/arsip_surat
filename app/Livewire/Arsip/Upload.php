<?php

namespace App\Livewire\Arsip;

use App\Models\Archive;
use App\Models\Category;
use App\Services\DocumentExtractorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    // Edit Mode properties
    public ?string $editId = null;
    public ?Archive $archive = null;

    // File upload
    public $file;

    // Form fields
    public string $nomor_surat = '';

    public string $tanggal_surat = '';

    public string $perihal = '';

    public string $jenis_surat = '';

    public string $kategori_id = '';

    public string $pengirim = '';

    public string $penerima = '';

    public string $keterangan = '';

    // Extraction status: null, 'success', 'partial', 'failed'
    public ?string $extractionStatus = null;

    // Jenis surat options
    public array $jenisOptions = [
        'masuk' => 'Surat Masuk',
        'keluar' => 'Surat Keluar',
        'lainnya' => 'SK / Lainnya',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->editId = $id;
            $this->archive = Archive::findOrFail($id);
            
            // Fill form
            $this->kategori_id = $this->archive->kategori_id;
            $this->jenis_surat = $this->archive->jenis_surat;
            $this->nomor_surat = $this->archive->main_meta['nomor_surat'] ?? '';
            $this->tanggal_surat = $this->archive->main_meta['tanggal'] ?? '';
            $this->perihal = $this->archive->main_meta['perihal'] ?? '';
            $this->pengirim = $this->archive->main_meta['pengirim'] ?? '';
            $this->penerima = $this->archive->main_meta['penerima'] ?? '';
            $this->keterangan = $this->archive->main_meta['keterangan'] ?? '';
        }
    }

    protected function rules(): array
    {
        $rules = [
            'nomor_surat' => 'required|string|max:100',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string|max:255',
            'jenis_surat' => 'required|in:masuk,keluar,sk,lainnya',
            'kategori_id' => 'required|exists:categories,_id',
            'pengirim' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ];

        // File required only on create (hanya PDF, DOC, DOCX)
        if (!$this->editId) {
            $rules['file'] = 'required|file|mimes:pdf,doc,docx|max:10240';
        } else {
            $rules['file'] = 'nullable|file|mimes:pdf,doc,docx|max:10240';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'file.required' => 'File dokumen wajib diupload.',
            'file.mimes' => 'Format file harus PDF, DOC, atau DOCX. File selain itu tidak diizinkan.',
            'file.max' => 'Ukuran file maksimal 10MB.',
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
            'perihal.required' => 'Perihal/judul surat wajib diisi.',
            'jenis_surat.required' => 'Jenis surat wajib dipilih.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',
        ];
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('kode')->get();
    }

    public function updatedFile()
    {
        $this->validateOnly('file');

        if ($this->file) {
            $this->extractMetadata();
        }
    }

    private function extractMetadata(): void
    {
        $extension = strtolower($this->file->getClientOriginalExtension());

        if (!in_array($extension, ['pdf', 'doc', 'docx'])) {
            return;
        }

        try {
            $extractor = new DocumentExtractorService();
            $result = $extractor->extract($this->file->getRealPath(), $extension);

            // Auto-fill hanya jika field masih kosong (tidak overwrite input manual)
            if (!empty($result['nomor_surat']) && empty($this->nomor_surat)) {
                $this->nomor_surat = $result['nomor_surat'];
            }
            if (!empty($result['tanggal_surat']) && empty($this->tanggal_surat)) {
                $this->tanggal_surat = $result['tanggal_surat'];
            }
            if (!empty($result['perihal']) && empty($this->perihal)) {
                $this->perihal = $result['perihal'];
            }

            // Hitung berapa field yang terdeteksi
            $detected = collect(['nomor_surat', 'tanggal_surat', 'perihal'])
                ->filter(fn ($key) => !empty($result[$key]))
                ->count();

            // Set extraction status untuk UI feedback
            $this->extractionStatus = match (true) {
                $detected === 3 => 'success',
                $detected >= 1 => 'partial',
                default => 'failed',
            };
        } catch (\Throwable) {
            $this->extractionStatus = 'failed';
        }
    }

    public function removeFile(): void
    {
        $this->file = null;
        $this->extractionStatus = null;
        $this->resetValidation('file');
    }

    public function save()
    {
        $validated = $this->validate();

        $mainMeta = [
            'nomor_surat' => $validated['nomor_surat'],
            'tanggal' => $validated['tanggal_surat'],
            'perihal' => $validated['perihal'],
            'pengirim' => $validated['pengirim'] ?? null,
            'penerima' => $validated['penerima'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
        ];

        if ($this->editId) {
            // Update Existing
            $updateData = [
                'kategori_id' => $validated['kategori_id'],
                'jenis_surat' => $validated['jenis_surat'],
                'main_meta' => $mainMeta,
            ];

            // Update file if new one uploaded
            if ($this->file) {
                // Delete old file
                if (isset($this->archive->file_info['path']) && Storage::disk('public')->exists($this->archive->file_info['path'])) {
                    Storage::disk('public')->delete($this->archive->file_info['path']);
                }

                // Upload new file
                $year = date('Y');
                $month = date('m');
                $extension = $this->file->getClientOriginalExtension();
                $randomName = Str::random(40).'.'.$extension;
                $directory = "archives/{$year}/{$month}";
                $filePath = "{$directory}/{$randomName}";
                
                $this->file->storeAs($directory, $randomName, 'public');

                $updateData['file_info'] = [
                    'path' => $filePath,
                    'original_name' => $this->file->getClientOriginalName(),
                    'size' => $this->file->getSize(),
                    'type' => $this->file->getMimeType(),
                ];
            }

            $this->archive->update($updateData);
            session()->flash('message', 'Arsip berhasil diperbarui!');
            
            // Redirect back based on type
            return redirect()->route('arsip.' . $this->jenis_surat);

        } else {
            // Create New
            $year = date('Y');
            $month = date('m');
            $extension = $this->file->getClientOriginalExtension();
            $randomName = Str::random(40).'.'.$extension;
            $directory = "archives/{$year}/{$month}";
            $filePath = "{$directory}/{$randomName}";

            $this->file->storeAs($directory, $randomName, 'public');

            Archive::create([
                'kategori_id' => $validated['kategori_id'],
                'uploader_id' => Auth::id(),
                'jenis_surat' => $validated['jenis_surat'],
                'main_meta' => $mainMeta,
                'file_info' => [
                    'path' => $filePath,
                    'original_name' => $this->file->getClientOriginalName(),
                    'size' => $this->file->getSize(),
                    'type' => $this->file->getMimeType(),
                ],
                'dynamic_meta' => [],
                'ocr_text' => null,
            ]);

            session()->flash('message', 'Arsip berhasil diupload!');
            
            // Reset form for create mode
            $this->reset([
                'file',
                'nomor_surat',
                'tanggal_surat',
                'perihal',
                'jenis_surat',
                'kategori_id',
                'pengirim',
                'penerima',
                'keterangan',
                'extractionStatus',
            ]);
        }
    }

    public function cancel()
    {
        if ($this->editId) {
            return redirect()->route('arsip.' . $this->jenis_surat);
        }

        $this->reset([
            'file',
            'nomor_surat',
            'tanggal_surat',
            'perihal',
            'jenis_surat',
            'kategori_id',
            'pengirim',
            'penerima',
            'keterangan',
            'extractionStatus',
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.arsip.upload');
    }
}

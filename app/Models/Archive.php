<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Model Archive - Arsip Digital Utama
 *
 * Menggunakan fitur schema-less MongoDB untuk metadata dinamis.
 *
 * @property string $_id
 * @property string $category_id Referensi ke categories
 * @property string $uploader_id Referensi ke users (yang upload)
 * @property object $main_meta Metadata wajib (nomor_surat, tanggal, perihal)
 * @property object $dynamic_meta Metadata fleksibel (NIK, Nama, Alamat, dll)
 * @property object $file_info Info file (path, size, type, original_name)
 * @property string $ocr_text Hasil ekstraksi teks untuk pencarian
 */
class Archive extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'archives';

    /**
     * Status arsip
     */
    public const STATUS_ACTIVE = 'aktif';
    public const STATUS_ARCHIVED = 'arsip';
    public const STATUS_DISPOSED = 'musnah';

    public const STATUSES = [
        self::STATUS_ACTIVE => ['label' => 'Aktif', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
        self::STATUS_ARCHIVED => ['label' => 'Arsip', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
        self::STATUS_DISPOSED => ['label' => 'Musnah', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'category_id',
        'uploader_id',
        'jenis_surat',
        'status',
        'main_meta',
        'dynamic_meta',
        'file_info',
        'ocr_text',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'main_meta' => 'array',
            'dynamic_meta' => 'array',
            'file_info' => 'array',
        ];
    }

    /**
     * Boot method - set default status
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($archive) {
            if (empty($archive->status)) {
                $archive->status = self::STATUS_ACTIVE;
            }
        });
    }

    /**
     * Get status badge classes
     */
    public function getStatusBadgeAttribute(): array
    {
        $status = $this->status ?? self::STATUS_ACTIVE;
        return self::STATUSES[$status] ?? self::STATUSES[self::STATUS_ACTIVE];
    }

    /**
     * Relasi: Archive dimiliki oleh Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relasi: Archive di-upload oleh User
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    /**
     * Relasi: Archive memiliki banyak Disposition
     */
    public function dispositions()
    {
        return $this->hasMany(Disposition::class, 'archive_id');
    }

    /**
     * Scope: Cari berdasarkan nomor surat
     */
    public function scopeByNomorSurat($query, string $nomor)
    {
        return $query->where('main_meta.nomor_surat', 'like', "%{$nomor}%");
    }

    /**
     * Scope: Cari berdasarkan perihal
     */
    public function scopeByPerihal($query, string $perihal)
    {
        return $query->where('main_meta.perihal', 'like', "%{$perihal}%");
    }

    /**
     * Scope: Full-text search pada OCR text
     */
    public function scopeSearchContent($query, string $keyword)
    {
        return $query->where('ocr_text', 'like', "%{$keyword}%");
    }

    /**
     * Scope: Filter berdasarkan jenis surat
     */
    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis_surat', $jenis);
    }

    /**
     * Cek apakah arsip memiliki disposisi aktif (pending/diteruskan)
     * 
     * Arsip dianggap memiliki disposisi aktif jika ada setidaknya satu
     * disposisi dengan status pending atau diteruskan.
     */
    public function hasActiveDisposition(): bool
    {
        return $this->dispositions()
            ->whereIn('status', [Disposition::STATUS_PENDING, Disposition::STATUS_DITERUSKAN])
            ->exists();
    }

    /**
     * Cek apakah arsip bisa didisposisikan
     * 
     * Arsip hanya bisa didisposisikan jika belum pernah ada disposisi sama sekali.
     * Sekali didisposisikan, tidak bisa didisposisikan ulang.
     */
    public function canBeDisposed(): bool
    {
        return $this->dispositions()->count() === 0;
    }

    /**
     * Get disposisi terakhir untuk arsip ini
     */
    public function latestDisposition()
    {
        return $this->dispositions()->latest()->first();
    }
}

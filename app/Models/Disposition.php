<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Model Disposition - Alur Disposisi Surat
 * 
 * @property string $_id
 * @property string $archive_id Referensi ke archives
 * @property string $sender_id Referensi ke users (pengirim)
 * @property string $receiver_id Referensi ke users (penerima)
 * @property string|null $parent_id Referensi ke disposisi induk (untuk chain)
 * @property string $instruction Isi instruksi disposisi
 * @property string $status Status disposisi (pending/diteruskan/selesai)
 * @property string|null $notes Catatan penyelesaian
 * @property \Carbon\Carbon|null $completed_at Waktu selesai
 */
class Disposition extends Model
{
    use HasFactory;

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_DITERUSKAN = 'diteruskan';
    public const STATUS_SELESAI = 'selesai';

    /**
     * All possible statuses with labels & badge styles
     */
    public const STATUSES = [
        self::STATUS_PENDING => ['label' => 'Menunggu', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
        self::STATUS_DITERUSKAN => ['label' => 'Diteruskan', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
        self::STATUS_SELESAI => ['label' => 'Selesai', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
    ];

    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'dispositions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'archive_id',
        'sender_id',
        'receiver_id',
        'parent_id',
        'instruction',
        'status',
        'notes',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    /**
     * Get status badge info
     */
    public function getStatusBadgeAttribute(): array
    {
        return self::STATUSES[$this->status] ?? self::STATUSES[self::STATUS_PENDING];
    }

    /**
     * Relasi: Disposition terkait dengan Archive
     */
    public function archive()
    {
        return $this->belongsTo(Archive::class, 'archive_id');
    }

    /**
     * Relasi: Disposition dikirim oleh User
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relasi: Disposition diterima oleh User
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Relasi: Disposisi induk (untuk chain/thread)
     */
    public function parent()
    {
        return $this->belongsTo(Disposition::class, 'parent_id');
    }

    /**
     * Relasi: Disposisi turunan
     */
    public function children()
    {
        return $this->hasMany(Disposition::class, 'parent_id');
    }

    /**
     * Scope: Filter status pending
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Filter status selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    /**
     * Scope: Filter status diteruskan
     */
    public function scopeDiteruskan($query)
    {
        return $query->where('status', self::STATUS_DITERUSKAN);
    }

    /**
     * Scope: Disposisi yang masih aktif (pending atau diteruskan)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_DITERUSKAN]);
    }

    /**
     * Mark disposition as selesai
     */
    public function markAsSelesai(?string $notes = null): bool
    {
        $this->status = self::STATUS_SELESAI;
        $this->notes = $notes;
        $this->completed_at = now();
        return $this->save();
    }

    /**
     * Mark disposition as diteruskan
     */
    public function markAsDiteruskan(): bool
    {
        $this->status = self::STATUS_DITERUSKAN;
        return $this->save();
    }

    /**
     * Cek apakah disposition masih pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Cek apakah disposition sudah selesai
     */
    public function isSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    /**
     * Cek apakah disposition sudah diteruskan
     */
    public function isDiteruskan(): bool
    {
        return $this->status === self::STATUS_DITERUSKAN;
    }
}

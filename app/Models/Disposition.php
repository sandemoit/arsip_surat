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
 * @property string $instruction Isi instruksi disposisi
 * @property string $status Status disposisi (pending/selesai)
 */
class Disposition extends Model
{
    use HasFactory;

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_SELESAI = 'selesai';

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
        'instruction',
        'status',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

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
     * Mark disposition as selesai
     */
    public function markAsSelesai(): bool
    {
        $this->status = self::STATUS_SELESAI;
        return $this->save();
    }

    /**
     * Cek apakah disposition masih pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}

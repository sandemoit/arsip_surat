<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Model Category - Klasifikasi Arsip
 * 
 * @property string $_id
 * @property string $kode Kode klasifikasi (misal: "470")
 * @property string $nama Nama klasifikasi (misal: "Kependudukan")
 * @property string $keterangan Deskripsi singkat kategori
 * @property int $masa_simpan Masa simpan arsip dalam tahun
 * @property string $warna Warna badge (blue, green, red, amber, purple, pink, indigo, cyan)
 */
class Category extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'kategori';

    /**
     * Available badge colors
     */
    public const COLORS = [
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
        'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
        'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
        'pink' => ['bg' => 'bg-pink-100', 'text' => 'text-pink-700'],
        'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
        'cyan' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-700'],
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'masa_simpan',
        'warna',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'masa_simpan' => 'integer',
        ];
    }

    /**
     * Get badge CSS classes
     */
    public function getBadgeClassesAttribute(): string
    {
        $warna = $this->warna ?? 'blue';
        $styles = self::COLORS[$warna] ?? self::COLORS['blue'];
        return $styles['bg'] . ' ' . $styles['text'];
    }

    /**
     * Relasi: Category memiliki banyak Archive
     */
    public function archives()
    {
        return $this->hasMany(Archive::class, 'kategori_id');
    }
}

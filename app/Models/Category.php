<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Model Category - Klasifikasi Arsip
 * 
 * @property string $_id
 * @property string $code Kode klasifikasi (misal: "470")
 * @property string $name Nama klasifikasi (misal: "Kependudukan")
 * @property int $retention_years Masa simpan arsip dalam tahun
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
    protected $collection = 'categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'retention_years',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'retention_years' => 'integer',
        ];
    }

    /**
     * Relasi: Category memiliki banyak Archive
     */
    public function archives()
    {
        return $this->hasMany(Archive::class, 'category_id');
    }
}

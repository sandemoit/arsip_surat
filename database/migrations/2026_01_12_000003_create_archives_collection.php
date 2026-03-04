<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     */
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     * 
     * Membuat collection archives dengan indexes untuk:
     * - Relasi ke category dan uploader
     * - Pencarian berdasarkan metadata (nomor surat, tanggal, perihal)
     * - Full-text search pada OCR text
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('archives', function (Blueprint $collection) {
            // Indexes untuk relasi
            $collection->index('kategori_id');
            $collection->index('uploader_id');
            
            // Indexes untuk metadata utama
            $collection->index('main_meta.nomor_surat');
            $collection->index('main_meta.tanggal');
            $collection->index('main_meta.perihal');
            
            // Index untuk pencarian tanggal (descending untuk sorting terbaru)
            $collection->index(['created_at' => -1]);
            
            // Compound index untuk filter kategori + tanggal
            $collection->index(['kategori_id' => 1, 'created_at' => -1]);
        });

        // Text index untuk full-text search pada OCR dan perihal
        // Menggunakan 'none' untuk language-agnostic (mendukung semua bahasa)
        \DB::connection('mongodb')->command([
            'createIndexes' => 'archives',
            'indexes' => [
                [
                    'key' => [
                        'ocr_text' => 'text',
                        'main_meta.perihal' => 'text',
                        'main_meta.nomor_surat' => 'text',
                    ],
                    'name' => 'archives_text_search',
                    'default_language' => 'none',
                    'weights' => [
                        'main_meta.nomor_surat' => 10,
                        'main_meta.perihal' => 5,
                        'ocr_text' => 1,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('archives');
    }
};

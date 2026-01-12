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
     * Membuat collection categories dengan indexes untuk pencarian cepat.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('categories', function (Blueprint $collection) {
            // Index unik untuk kode kategori
            $collection->unique('code');
            
            // Index untuk pencarian nama
            $collection->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('categories');
    }
};

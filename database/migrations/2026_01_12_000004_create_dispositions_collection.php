<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     */
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     *
     * Membuat collection dispositions dengan indexes untuk:
     * - Relasi ke archive, sender, receiver
     * - Filter berdasarkan status
     * - Sorting berdasarkan waktu
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('dispositions', function (Blueprint $collection) {
            // Indexes untuk relasi
            $collection->index('archive_id');
            $collection->index('sender_id');
            $collection->index('receiver_id');

            // Index untuk filter status
            $collection->index('status');

            // Compound index untuk inbox penerima (receiver + status)
            $collection->index(['receiver_id' => 1, 'status' => 1]);

            // Compound index untuk outbox pengirim (sender + created_at)
            $collection->index(['sender_id' => 1, 'created_at' => -1]);

            // Index untuk sorting waktu
            $collection->index(['created_at' => -1]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('dispositions');
    }
};

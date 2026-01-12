<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Membuat TTL Index untuk MongoDB Cache, Locks, dan Sessions.
     * TTL Index memungkinkan MongoDB secara otomatis menghapus
     * dokumen yang sudah expired untuk performa yang lebih baik.
     */
    public function up(): void
    {
        // TTL Index untuk Cache
        $store = Cache::store('mongodb');
        $store->createTTLIndex();
        $store->lock('')->createTTLIndex();

        // TTL Index untuk Sessions
        Schema::connection('mongodb')->table('sessions', function (Blueprint $collection) {
            $collection->expire('expires_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // TTL indexes akan otomatis terhapus jika collection di-drop
    }
};

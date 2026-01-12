<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\RoleService;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     */
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->table('users', function (Blueprint $collection) {
            // Tambah index untuk role
            $collection->index('role');
            $collection->index('nip');
        });

        // Update existing users dengan default role
        \App\Models\User::whereNull('role')->update([
            'role' => RoleService::STAF,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->table('users', function (Blueprint $collection) {
            $collection->dropIndex('role_1');
            $collection->dropIndex('nip_1');
        });
    }
};

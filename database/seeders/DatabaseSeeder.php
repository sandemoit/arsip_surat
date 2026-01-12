<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Services\RoleService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Admin User
        User::firstOrCreate(
            ['email' => 'admin@arsipsurat.test'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => RoleService::ADMIN,
                'nip' => '000000000',
            ]
        );

        // Seed Staf User (untuk testing)
        User::firstOrCreate(
            ['email' => 'staf@arsipsurat.test'],
            [
                'name' => 'Staf Kecamatan',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => RoleService::STAF,
                'nip' => '111111111',
            ]
        );

        // Seed Kategori Default
        $categories = [
            ['code' => '000', 'name' => 'Umum', 'retention_years' => 5],
            ['code' => '100', 'name' => 'Kepegawaian', 'retention_years' => 10],
            ['code' => '200', 'name' => 'Keuangan', 'retention_years' => 10],
            ['code' => '300', 'name' => 'Peralatan', 'retention_years' => 5],
            ['code' => '400', 'name' => 'Pembangunan', 'retention_years' => 10],
            ['code' => '470', 'name' => 'Kependudukan', 'retention_years' => 10],
            ['code' => '500', 'name' => 'Kesejahteraan Rakyat', 'retention_years' => 5],
            ['code' => '600', 'name' => 'Pemerintahan', 'retention_years' => 10],
            ['code' => '700', 'name' => 'Perizinan', 'retention_years' => 5],
            ['code' => '800', 'name' => 'Keamanan dan Ketertiban', 'retention_years' => 5],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['code' => $category['code']],
                $category
            );
        }

        $this->command->info('Seeding selesai:');
        $this->command->info('- Admin: admin@arsipsurat.test / password');
        $this->command->info('- Staf: staf@arsipsurat.test / password');
        $this->command->info('- ' . count($categories) . ' kategori arsip');
    }
}


<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
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
            ['email' => 'admin@gmail.com'],
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
            ['email' => 'staf1@gmail.com'],
            [
                'name' => 'Staf Satu',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => RoleService::STAF,
                'nip' => '111111111',
            ]
        );

        // Seed Staf User (untuk testing 2)
        User::firstOrCreate(
            ['email' => 'staf2@gmail.com'],
            [
                'name' => 'Staf Dua',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => RoleService::STAF,
                'nip' => '222222222',
            ]
        );

        // Seed Kategori Default
        $categories = [
            ['code' => '000', 'name' => 'Umum', 'description' => 'Surat undangan, pemberitahuan, pengumuman', 'retention_years' => 5],
            ['code' => '100', 'name' => 'Kepegawaian', 'description' => 'SK Pegawai, cuti, mutasi, pensiun', 'retention_years' => 10],
            ['code' => '200', 'name' => 'Keuangan', 'description' => 'Laporan, SPJ, nota dinas keuangan', 'retention_years' => 10],
            ['code' => '300', 'name' => 'Peralatan', 'description' => 'Inventaris, pengadaan, pemeliharaan', 'retention_years' => 5],
            ['code' => '400', 'name' => 'Pembangunan', 'description' => 'Proyek, infrastruktur, perencanaan', 'retention_years' => 10],
            ['code' => '470', 'name' => 'Kependudukan', 'description' => 'Surat keterangan, KTP, KK, dokumen kependudukan', 'retention_years' => 10],
            ['code' => '500', 'name' => 'Kesejahteraan Rakyat', 'description' => 'Bantuan sosial, kesehatan, pendidikan', 'retention_years' => 5],
            ['code' => '600', 'name' => 'Pemerintahan', 'description' => 'Kebijakan, peraturan, keputusan', 'retention_years' => 10],
            ['code' => '700', 'name' => 'Perizinan', 'description' => 'IMB, izin usaha, rekomendasi', 'retention_years' => 5],
            ['code' => '800', 'name' => 'Keamanan dan Ketertiban', 'description' => 'Laporan keamanan, koordinasi linmas', 'retention_years' => 5],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['code' => $category['code']],
                $category
            );
        }

        $this->command->info('Seeding selesai:');
        $this->command->info('- Admin: admin@gmail.com / password');
        $this->command->info('- Staf: staf@arsipsurat.test / password');
        $this->command->info('- '.count($categories).' kategori arsip');
    }
}

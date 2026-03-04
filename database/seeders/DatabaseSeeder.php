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
            ['kode' => '000', 'nama' => 'Umum', 'keterangan' => 'Surat undangan, pemberitahuan, pengumuman', 'masa_simpan' => 5],
            ['kode' => '100', 'nama' => 'Kepegawaian', 'keterangan' => 'SK Pegawai, cuti, mutasi, pensiun', 'masa_simpan' => 10],
            ['kode' => '200', 'nama' => 'Keuangan', 'keterangan' => 'Laporan, SPJ, nota dinas keuangan', 'masa_simpan' => 10],
            ['kode' => '300', 'nama' => 'Peralatan', 'keterangan' => 'Inventaris, pengadaan, pemeliharaan', 'masa_simpan' => 5],
            ['kode' => '400', 'nama' => 'Pembangunan', 'keterangan' => 'Proyek, infrastruktur, perencanaan', 'masa_simpan' => 10],
            ['kode' => '470', 'nama' => 'Kependudukan', 'keterangan' => 'Surat keterangan, KTP, KK, dokumen kependudukan', 'masa_simpan' => 10],
            ['kode' => '500', 'nama' => 'Kesejahteraan Rakyat', 'keterangan' => 'Bantuan sosial, kesehatan, pendidikan', 'masa_simpan' => 5],
            ['kode' => '600', 'nama' => 'Pemerintahan', 'keterangan' => 'Kebijakan, peraturan, keputusan', 'masa_simpan' => 10],
            ['kode' => '700', 'nama' => 'Perizinan', 'keterangan' => 'IMB, izin usaha, rekomendasi', 'masa_simpan' => 5],
            ['kode' => '800', 'nama' => 'Keamanan dan Ketertiban', 'keterangan' => 'Laporan keamanan, koordinasi linmas', 'masa_simpan' => 5],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['kode' => $category['kode']],
                $category
            );
        }

        $this->command->info('Seeding selesai:');
        $this->command->info('- Admin: admin@gmail.com / password');
        $this->command->info('- Staf: staf@arsipsurat.test / password');
        $this->command->info('- '.count($categories).' kategori arsip');
    }
}

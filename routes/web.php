<?php

use App\Livewire\Arsip\Keluar as ArsipKeluar;
use App\Livewire\Arsip\Lainnya as ArsipLainnya;
use App\Livewire\Arsip\Masuk as ArsipMasuk;
use App\Livewire\Arsip\Search as ArsipSearch;
use App\Livewire\Arsip\Upload as ArsipUpload;
use App\Livewire\Dashboard;
use App\Livewire\Disposisi\Masuk as DisposisiMasuk;
use App\Livewire\Disposisi\Riwayat as DisposisiRiwayat;
use App\Livewire\Pengaturan\Pengguna;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Settings (Volt - simple)
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // ==========================================
    // ARSIP ROUTES (Livewire Component)
    // ==========================================
    Route::get('arsip/upload', ArsipUpload::class)->name('arsip.upload');
    Route::get('arsip/search', ArsipSearch::class)->name('arsip.search');
    Route::get('arsip/masuk', ArsipMasuk::class)->name('arsip.masuk');
    Route::get('arsip/keluar', ArsipKeluar::class)->name('arsip.keluar');
    Route::get('arsip/lainnya', ArsipLainnya::class)->name('arsip.lainnya');

    // ==========================================
    // DISPOSISI ROUTES (Livewire Component)
    // ==========================================
    Route::get('disposisi/masuk', DisposisiMasuk::class)->name('disposisi.masuk');
    Route::get('disposisi/riwayat', DisposisiRiwayat::class)->name('disposisi.riwayat');

    // ==========================================
    // PENGATURAN ROUTES (Admin Only)
    // ==========================================
    Route::middleware(['role:admin'])->group(function () {
        // Livewire Component (kompleks)
        Route::get('pengaturan/pengguna', Pengguna::class)->name('pengguna.index');

        // Volt (simple CRUD)
        Volt::route('pengaturan/kategori', 'pengaturan.kategori')->name('kategori.index');
    });
});

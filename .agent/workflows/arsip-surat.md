---
description: Konsep Dasar Web app arsip surat
---

# SI-ARSIP KELEKAR

Sistem Informasi Arsip Digital untuk Kantor Kecamatan Kelekar.

## Tech Stack

-   **Framework**: Laravel 12 + Livewire Volt
-   **UI**: Flux UI Components
-   **Database**: MongoDB (mongodb/laravel-mongodb v5.5)
-   **Auth**: Laravel Fortify

## Struktur Database (MongoDB Collections)

### 1. users

| Field    | Type     | Keterangan          |
| -------- | -------- | ------------------- |
| \_id     | ObjectId | Primary Key         |
| name     | String   | Nama lengkap        |
| email    | String   | Email login         |
| password | String   | Hashed password     |
| role     | String   | "admin" atau "staf" |
| nip      | String   | Nomor Induk Pegawai |

### 2. categories

| Field           | Type     | Keterangan                      |
| --------------- | -------- | ------------------------------- |
| \_id            | ObjectId | Primary Key                     |
| code            | String   | Kode klasifikasi (misal: "470") |
| name            | String   | Nama kategori                   |
| retention_years | Integer  | Masa simpan (tahun)             |

### 3. archives

| Field        | Type     | Keterangan                            |
| ------------ | -------- | ------------------------------------- |
| \_id         | ObjectId | Primary Key                           |
| category_id  | ObjectId | Ref ke categories                     |
| uploader_id  | ObjectId | Ref ke users                          |
| main_meta    | Object   | {nomor_surat, tanggal, perihal}       |
| dynamic_meta | Object   | Metadata fleksibel (NIK, Alamat, dll) |
| file_info    | Object   | {path, size, type, original_name}     |
| ocr_text     | String   | Hasil ekstraksi teks                  |

### 4. dispositions

| Field       | Type     | Keterangan               |
| ----------- | -------- | ------------------------ |
| \_id        | ObjectId | Primary Key              |
| archive_id  | ObjectId | Ref ke archives          |
| sender_id   | ObjectId | Pengirim                 |
| receiver_id | ObjectId | Penerima                 |
| instruction | String   | Instruksi disposisi      |
| status      | String   | "pending" atau "selesai" |

## Role & Permissions

| Fitur           | Admin | Staf |
| --------------- | ----- | ---- |
| Dashboard       | ✅    | ✅   |
| Upload Arsip    | ✅    | ✅   |
| Pencarian Arsip | ✅    | ✅   |
| Data Arsip      | ✅    | ✅   |
| Disposisi       | ✅    | ✅   |
| Kelola Pengguna | ✅    | ❌   |
| Kelola Kategori | ✅    | ❌   |

## Struktur Menu Sidebar

**MENU UTAMA**

-   Dashboard
-   Upload Arsip
-   Pencarian Arsip
-   Data Arsip (dropdown)
    -   Surat Masuk
    -   Surat Keluar
    -   SK / Lainnya

**ADMINISTRASI**

-   Disposisi (dropdown)
    -   Kotak Masuk
    -   Riwayat
-   Pengaturan (dropdown) _admin only_

    -   Data Pengguna
    -   Kategori Arsip

-   Logout

## Konvensi Kode

### Routes (Livewire Volt)

```php
// routes/web.php
Volt::route('pengaturan/kategori', 'pengaturan.kategori')
    ->middleware(['auth', 'role:admin'])
    ->name('kategori.index');
```

### Livewire Volt Component

```php
// resources/views/livewire/pengaturan/kategori.blade.php
<?php
use Livewire\Volt\Component;
use App\Models\Category;

new class extends Component {
    public function with(): array {
        return ['categories' => Category::paginate(10)];
    }
}; ?>

<div>
    <flux:table :paginate="$categories">
        ...
    </flux:table>
</div>
```

### Kapan Pakai Volt vs Livewire Component

| Tipe                   | Gunakan                          | Contoh                            |
| ---------------------- | -------------------------------- | --------------------------------- |
| **Volt** (Single-file) | CRUD simple, handler ringan      | Kategori, placeholder pages       |
| **Livewire Component** | Fitur kompleks, banyak interaksi | Upload Arsip, Disposisi, Pengguna |

**Volt** → handler langsung di blade file
**Livewire Component** → `php artisan make:livewire Arsip/Upload`

### Middleware Role

```php
// bootstrap/app.php
$middleware->alias(['role' => \App\Http\Middleware\CheckRole::class]);

// Penggunaan di route
->middleware(['auth', 'role:admin'])
->middleware(['auth', 'role:admin,staf'])
```

## File Penting

| Path                                                       | Keterangan                            |
| ---------------------------------------------------------- | ------------------------------------- |
| `app/Services/RoleService.php`                             | Constants & helper role               |
| `app/Http/Middleware/CheckRole.php`                        | Middleware proteksi role              |
| `app/Models/User.php`                                      | Model user dengan isAdmin(), isStaf() |
| `app/Models/Category.php`                                  | Model kategori arsip                  |
| `app/Models/Archive.php`                                   | Model arsip utama                     |
| `app/Models/Disposition.php`                               | Model disposisi                       |
| `resources/views/components/layouts/app/sidebar.blade.php` | Sidebar layout                        |

## Referensi

-   MongoDB Laravel: https://www.mongodb.com/docs/drivers/php/laravel-mongodb
-   Flux UI: https://fluxui.dev
-   Livewire Volt: https://livewire.laravel.com/docs/volt

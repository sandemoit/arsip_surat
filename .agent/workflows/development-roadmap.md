---
description: Roadmap pengembangan fitur SI-ARSIP KELEKAR
---

# Development Roadmap - SI-ARSIP KELEKAR

Workflow ini berisi urutan pengembangan fitur aplikasi arsip surat dari yang sudah selesai hingga yang masih perlu dibangun.

## ✅ FASE 1: MASTER DATA (SELESAI)

### 1.1 Kelola Kategori Arsip

-   [x] CRUD kategori (kode, nama, deskripsi, masa simpan)
-   [x] Validasi kode unik
-   [x] Sorting & pagination
-   [x] Counter jumlah arsip per kategori

**File terkait:**

-   `resources/views/livewire/pengaturan/kategori.blade.php` (Volt)
-   `app/Models/Category.php`

### 1.2 Kelola Pengguna

-   [x] CRUD pengguna (nama, email, password, NIP, role)
-   [x] Field status aktif/nonaktif
-   [x] Field last login
-   [x] Reset password
-   [x] Toggle status aktif
-   [x] Proteksi: tidak bisa hapus/nonaktifkan diri sendiri

**File terkait:**

-   `app/Livewire/Pengaturan/Pengguna.php`
-   `resources/views/livewire/pengaturan/pengguna.blade.php`
-   `app/Models/User.php`

---

## 🚧 FASE 2: MANAJEMEN ARSIP (PRIORITAS TINGGI)

### 2.1 Upload Arsip

**Estimasi:** 2-3 jam

**Fitur:**

-   Form upload file (PDF, JPG, PNG)
-   Input metadata wajib:
    -   Nomor surat
    -   Tanggal surat
    -   Perihal
    -   Kategori (dropdown dari master kategori)
-   Input metadata dinamis (sesuai kategori):
    -   NIK, Alamat, dll (opsional)
-   Validasi:
    -   File max 10MB
    -   Format file yang diizinkan
    -   Nomor surat unik per tahun
-   Preview file sebelum upload
-   OCR text extraction (fase lanjutan - opsional)

**File yang perlu dibuat/edit:**

-   `app/Livewire/Arsip/Upload.php` (sudah ada, perlu implementasi)
-   `resources/views/livewire/arsip/upload.blade.php`
-   `app/Models/Archive.php`
-   Storage: `storage/app/archives/{year}/{month}/`

**Referensi layout:**

-   `referensi_layout/upload-arsip.html`

---

### 2.2 Data Arsip - Surat Masuk

**Estimasi:** 2-3 jam

**Fitur:**

-   Tabel listing arsip surat masuk
-   Kolom: No, Nomor Surat, Tanggal, Perihal, Kategori, Uploader, Aksi
-   Filter by:
    -   Kategori
    -   Tanggal (range)
    -   Uploader
-   Sorting
-   Pagination
-   Aksi:
    -   View detail (modal)
    -   Download file
    -   Edit metadata
    -   Delete (dengan konfirmasi)
-   Badge status disposisi (jika ada)

**File yang perlu dibuat/edit:**

-   `app/Livewire/Arsip/Masuk.php`
-   `resources/views/livewire/arsip/masuk.blade.php`

**Referensi layout:**

-   `referensi_layout/data-surat-masuk.html`

---

### 2.3 Data Arsip - Surat Keluar

**Estimasi:** 1-2 jam (mirip dengan Surat Masuk)

**Fitur:** (sama seperti Surat Masuk, tapi filter by jenis = keluar)

**File yang perlu dibuat/edit:**

-   `app/Livewire/Arsip/Keluar.php`
-   `resources/views/livewire/arsip/keluar.blade.php`

**Referensi layout:**

-   `referensi_layout/data-surat-keluar.html`

---

### 2.4 Data Arsip - SK / Lainnya

**Estimasi:** 1-2 jam (mirip dengan Surat Masuk)

**Fitur:** (sama seperti Surat Masuk, tapi filter by jenis = lainnya)

**File yang perlu dibuat/edit:**

-   `app/Livewire/Arsip/Lainnya.php`
-   `resources/views/livewire/arsip/lainnya.blade.php`

**Referensi layout:**

-   `referensi_layout/data-sk-lainnya.html`

---

### 2.5 Pencarian Arsip

**Estimasi:** 2-3 jam

**Fitur:**

-   Search box global
-   Pencarian by:
    -   Nomor surat (exact/partial match)
    -   Perihal (full-text)
    -   Tanggal (range)
    -   Kategori
    -   Uploader
    -   OCR text (jika sudah implementasi OCR)
-   Advanced search (toggle)
-   Hasil pencarian dalam tabel
-   Export hasil pencarian (CSV/Excel - opsional)

**File yang perlu dibuat/edit:**

-   `app/Livewire/Arsip/Search.php`
-   `resources/views/livewire/arsip/search.blade.php`

**Referensi layout:**

-   `referensi_layout/pencarian-arsip.html`

---

## 🚧 FASE 3: DISPOSISI (PRIORITAS SEDANG)

### 3.1 Disposisi - Kotak Masuk

**Estimasi:** 3-4 jam

**Fitur:**

-   Tabel disposisi yang diterima user
-   Kolom: No, Arsip, Pengirim, Instruksi, Tanggal, Status, Aksi
-   Filter by status (pending/selesai)
-   Aksi:
    -   View detail arsip
    -   Tandai selesai
    -   Teruskan ke user lain (disposisi lanjutan)
-   Notifikasi disposisi baru

**File yang perlu dibuat/edit:**

-   `app/Livewire/Disposisi/Masuk.php`
-   `resources/views/livewire/disposisi/masuk.blade.php`
-   `app/Models/Disposition.php`

**Referensi layout:**

-   `referensi_layout/disposisi-masuk.html`

---

### 3.2 Disposisi - Riwayat

**Estimasi:** 2-3 jam

**Fitur:**

-   Tabel riwayat disposisi yang dikirim user
-   Kolom: No, Arsip, Penerima, Instruksi, Tanggal, Status
-   Filter by:
    -   Status
    -   Tanggal (range)
    -   Penerima
-   View tracking disposisi (siapa saja yang sudah menerima)

**File yang perlu dibuat/edit:**

-   `app/Livewire/Disposisi/Riwayat.php`
-   `resources/views/livewire/disposisi/riwayat.blade.php`

**Referensi layout:**

-   `referensi_layout/disposisi-riwayat.html`

---

### 3.3 Kirim Disposisi (dari Detail Arsip)

**Estimasi:** 2 jam

**Fitur:**

-   Modal kirim disposisi dari halaman detail arsip
-   Form:
    -   Pilih penerima (dropdown user)
    -   Instruksi disposisi (textarea)
    -   Prioritas (normal/urgent - opsional)
-   Validasi: tidak bisa kirim ke diri sendiri
-   Notifikasi ke penerima

**File yang perlu dibuat/edit:**

-   Component modal disposisi
-   Handler di Arsip detail

---

## 🚧 FASE 4: DASHBOARD & REPORTING (PRIORITAS RENDAH)

### 4.1 Dashboard

**Estimasi:** 2-3 jam

**Fitur:**

-   Statistik cards:
    -   Total arsip
    -   Arsip bulan ini
    -   Disposisi pending
    -   User aktif (admin only)
-   Chart:
    -   Arsip per kategori (pie/donut chart)
    -   Trend upload per bulan (line chart)
-   Tabel arsip terbaru (5 terakhir)
-   Tabel disposisi pending (5 teratas)
-   Quick actions (upload, search)

**File yang perlu dibuat/edit:**

-   `resources/views/dashboard.blade.php` (sudah ada, perlu implementasi)
-   Chart library: Chart.js atau ApexCharts

**Referensi layout:**

-   `referensi_layout/dashboard.html`

---

### 4.2 Laporan (Opsional - Fase Lanjutan)

**Estimasi:** 3-4 jam

**Fitur:**

-   Laporan arsip per periode
-   Laporan disposisi per user
-   Export PDF/Excel
-   Filter custom

---

## 🚧 FASE 5: FITUR LANJUTAN (OPSIONAL)

### 5.1 OCR Text Extraction

-   Ekstrak teks dari PDF/gambar
-   Simpan di field `ocr_text`
-   Gunakan untuk pencarian full-text
-   Library: Tesseract OCR

### 5.2 Notifikasi Real-time

-   WebSocket/Pusher untuk notifikasi real-time
-   Notifikasi disposisi baru
-   Notifikasi arsip baru (untuk admin)

### 5.3 Audit Log

-   Track semua aktivitas user
-   View/download/edit/delete arsip
-   Kirim/terima disposisi

### 5.4 Backup & Restore

-   Scheduled backup database
-   Backup file arsip
-   Restore functionality

### 5.5 Multi-tenant (jika diperlukan)

-   Support multiple kantor/instansi
-   Isolasi data per tenant

---

## 📝 Catatan Pengembangan

### Urutan Rekomendasi:

1. ✅ Master Data (Selesai)
2. 🔥 Upload Arsip (Prioritas #1)
3. 🔥 Data Arsip - Surat Masuk (Prioritas #2)
4. Data Arsip - Surat Keluar
5. Data Arsip - SK/Lainnya
6. Pencarian Arsip
7. Dashboard
8. Disposisi - Kotak Masuk
9. Disposisi - Riwayat
10. Fitur lanjutan (sesuai kebutuhan)

### Teknologi yang Digunakan:

-   **Backend**: Laravel 12 + Livewire Volt
-   **Frontend**: Flux UI Components
-   **Database**: MongoDB
-   **Storage**: Local storage (Laravel Storage)
-   **File Upload**: Livewire File Upload
-   **Charts**: Chart.js / ApexCharts (untuk dashboard)

### Konvensi:

-   Gunakan Livewire Component (bukan Volt) untuk fitur kompleks (Upload, Disposisi)
-   Gunakan Volt untuk fitur simple (listing, CRUD sederhana)
-   Semua file upload disimpan di `storage/app/archives/`
-   Naming: `{year}/{month}/{random_name}.{ext}`
-   Validasi file: PDF, JPG, PNG, max 10MB

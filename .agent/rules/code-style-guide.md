---
trigger: always_on
---

# 📐 CODE STYLE & ENGINEERING STANDARDS

Dokumen ini menjadi acuan standar penulisan kode untuk proyek **E-Arsip Digital**. Tujuannya adalah menjaga konsistensi, keterbacaan (_readability_), dan kemudahan perawatan (_maintainability_) aplikasi.
Jangan testing sebelum aku suruh testing. Karena aku lebih suka testing manual sendiri.

---

## 1. General Philosophy

- **KISS (Keep It Simple, Stupid):** Jangan membuat solusi kompleks untuk masalah sederhana.
- **DRY (Don't Repeat Yourself):** Jika kode ditulis dua kali, refactor menjadi fungsi atau komponen.
- **Modern PHP:** Manfaatkan fitur PHP 8.2+ (Constructor Promotion, Match Expressions, Type Hinting).

---

## 2. PHP & Laravel Conventions

### A. Naming Conventions

| Type                | Case Style | Example                                  |
| :------------------ | :--------- | :--------------------------------------- |
| Class / Model       | PascalCase | `ArchiveDocument`, `UserLog`             |
| Method / Function   | camelCase  | `storeMetadata()`, `getLatestArchives()` |
| Variable            | camelCase  | `$archiveData`, `$userList`              |
| Database Collection | snake_case | `archives`, `activity_logs`              |
| Route Names         | kebab-case | `archives.create`, `settings.profile`    |

### B. Controller Logic

Hindari _logic_ yang terlalu gemuk di Controller. Gunakan pendekatan **Early Return** untuk mengurangi _nesting_ `if-else`.

**❌ Bad:**

```php
public function store(Request $request) {
    if ($request->hasFile('file')) {
        if ($user->isAdmin()) {
            // Logic simpan...
        } else {
            return redirect()->back();
        }
    }
}

---

## 3. Core Engineering Values (The "Clean Code" Mantra)

Setiap baris kode yang ditulis dalam proyek ini harus mematuhi prinsip-prinsip berikut:

### 💎 Clean & Readable
* **Code is for Humans:** Kode harus mudah dibaca oleh manusia, bukan hanya mesin. Penamaan variabel dan fungsi harus deskriptif (*Self-documenting code*).
* **No Magic Numbers:** Hindari angka atau string misterius di tengah logika. Gunakan Konstanta atau Enum.

### ♻️ Reusable & Modular
* **Component Driven:** Jangan menulis UI/Logic yang sama berulang kali. Pecah menjadi komponen (Blade/Livewire) atau Service Class yang bisa dipakai ulang.
* **Single Responsibility Principle:** Satu fungsi/class hanya boleh melakukan satu hal. Jika satu fungsi sudah lebih dari 20 baris, pertimbangkan untuk memecahnya.

###  DRY (Don't Repeat Yourself)
* **Duplication is the Enemy:** Jika Anda menyalin-tempel (*copy-paste*) kode yang sama di dua tempat berbeda, itu adalah tanda teknikal debt. Abstraksikan menjadi fungsi atau *trait*.

### 📈 Scalable & Best Practices
* **Future-Proof:** Desain sistem harus siap menangani pertumbuhan data. Gunakan *Eager Loading* (`with()`) untuk menghindari masalah N+1 Query pada MongoDB.
* **Standard Compliant:** Ikuti standar PSR-12 untuk PHP dan struktur folder default Laravel 12. Konsistensi adalah kunci skalabilitas tim.
```

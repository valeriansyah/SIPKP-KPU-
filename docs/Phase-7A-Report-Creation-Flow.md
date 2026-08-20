# Phase 7A — Report Creation Flow Architecture & Implementation

## 1. Audit Result
- **Existing**: `Report`, `Deceased`, `Document` models dan migrations. `ReportService::createReport` tersedia namun belum menangani upload dokumen secara atomic. `ReportController::store` dan `create` memiliki endpoint tetapi View `create.blade.php` masih berupa form kosong.
- **Missing**: Form UI lengkap untuk Pelapor. Validasi file pada `StoreReportRequest`. Integrasi upload dokumen ke dalam proses `DB::transaction` saat pembuatan laporan untuk menjaga atomicity.

## 2. Architecture Decision
- Diputuskan menggunakan pendekatan **Single-page vertical form** (seksi vertikal) dibandingkan multi-step form murni, untuk memastikan keandalan UX dan penyerahan payload serentak tanpa memerlukan manajemen _state_ sesi kompleks.

## 3. Files Created & Modified
- **Modified**: `app/Http/Requests/StoreReportRequest.php` - Penambahan aturan validasi ukuran dan ekstensi file (maks 5MB, mimes:pdf,jpg,png).
- **Modified**: `app/Services/ReportService.php` - Injeksi `DocumentService` dan refaktorisasi metode `createReport` agar menerima array `$files` untuk memastikan upload file fisik dan entri metadata dokumen di eksekusi bersamaan dengan rekam data _Deceased_ dan _Report_ dalam satu `DB::transaction`.
- **Modified**: `app/Http/Controllers/ReportController.php` - Pengiriman referensi Master `District` dan `DocumentType` ke Blade, serta injeksi `$request->file('documents')` ke `ReportService`.
- **Created/Overwritten**: `resources/views/pelapor/laporan/create.blade.php` - Menyusun Form UI berbasis grid dengan membagi bagian Data Pelapor (readonly), Data Almarhum, dan Upload Dokumen (wajib dan opsional dilabeli jelas).
- **Created**: `tests/Feature/ReportCreationFlowTest.php` - Test suite eksklusif.
- **Modified**: `tests/Feature/ReportServiceTest.php` - Penyesuaian skenario test pasca refaktorisasi (menambahkan mock file payload).

## 4. Contract Fulfillment
- **Request Contract**: Mengikat input form `nik`, `family_card_number`, `documents[1]` dll, sesuai aturan wajib dan opsional di `DocumentTypeSeeder`.
- **Controller & Service Contract**: Controller membersihkan Request dan mendelegasikan ke Service yang mengelola Transaksi DB. Data district didapat langsung dari payload, sedangkan data User (`user_id`) didapat mutlak dari Session `Auth::user()` menghindari kebocoran spoofing.

## 5. Security Validation
1. **User Auth**: Akses endpoint terproteksi.
2. **Authorization**: Sub-operator dan Operator Provinsi ditolak mengakses Form Create Pelapor (Assertion 403 Forbidden lulus).
3. **Atomicity**: Integrasi Dokumen di dalam transaksi tunggal lulus.

## 6. Automated Test & Regression
**Previous Baseline**: 137 tests / 331 assertions
**Current Status**: 142 tests / 353 assertions (ALL PASSED, 0 FAILED)

## 7. Database Integrity
Schema Database dan Migrations sama sekali tidak disentuh atau diubah. Arsitektur tetap utuh sesuai kontrak awal.

## 8. Final Verdict
Phase 7A selesai dengan sukses tanpa regresi. Modul Pembuatan Laporan oleh Pelapor berfungsi penuh secara End-to-End.
READY FOR PHASE 7B.

# PHASE 7C — DOCUMENT UPLOAD UX & VALIDATION

## 1. Audit Result
Struktur backend (StoreReportRequest & ReportService) telah mengunci validasi jenis file, limitasi `size`, serta kewajiban parameter dari fase sebelumnya. Atomicity file *storage* dan *database* berisiko melepaskan *orphaned files* di server apabila `database transaction` mengalami rollback setelah file diletakkan di *storage*. Pada sisi UX, antarmuka `Document Upload` masih minim interaktivitas tanpa indikator drag & drop.

## 2. Files Created
1. `tests/Feature/DocumentUploadValidationTest.php` (Test case komprehensif validasi dokumen)

## 3. Files Modified
1. `app/Services/ReportService.php` (Penambahan `try-catch` cleanup *orphaned files*)
2. `resources/views/pelapor/laporan/create.blade.php` (Menyusun DOM Vanilla JS dan membagi struktur komponen opsional/wajib)
3. `resources/views/pelapor/laporan/partials/upload-card.blade.php` (Pembuatan HTML Card individual per jenis file)

## 4. Document Type Mapping
*Source of Truth: `DocumentTypeSeeder`*
1. Surat Keterangan Kematian (Wajib)
2. KTP Almarhum (Wajib)
3. Kartu Keluarga (Wajib)
4. Surat Pengantar RT/RW (Opsional)
5. Surat Visum (Opsional)
6. KTP Pelapor (Wajib)
7. Akta Kelahiran Almarhum (Opsional)
8. Foto Almarhum (Opsional)

## 5. UI/UX Result
Bagian `DOKUMEN PENDUKUNG` pada form kini dipecah menjadi dua wilayah (Dokumen Wajib & Dokumen Opsional). Komponen `<input type="file">` bawaan HTML disamarkan menggunakan gaya Card modern. Tersedia 3 status visual interaktif:
- **BELUM DIUNGGAH**: Menampilkan area *Drag-and-Drop* dengan panduan format file dan limit (Maks 5 MB).
- **READY / FILE DIPILIH**: Membuka blok hijau persis ketika file dimasukkan, menyuguhkan pratinjau instan (*filename* & kapasitas) serta tombol "Ganti" dan "Hapus".
- **ERROR**: Melontarkan teguran merah apabila ukuran atau format melampaui standar dari sisi *client* JS. 

## 6. Frontend Validation
Hanya berlaku untuk kepentingan UX. Menggunakan Vanilla JS untuk:
- Mengecek eksistensi dan tipe *extension* `.pdf`, `.jpg`, `.png`.
- Memeriksa agar *file size* < 5MB sebelum backend dipanggil.

## 7. Backend Validation
Backend tetap sebagai kunci ganda (*Source of Truth*). `StoreReportRequest` tidak termodifikasi, karena fungsionalitas `max:5120` dan `mimes:pdf,jpg,jpeg,png` untuk seluruh array document sudah diimplementasikan di Phase 7A secara *strict*.

## 8. Security Validation
Test regresi telah mensertifikasi bahwa:
1. Akses selain dari `Pelapor` yang berwenang akan dibungkam *403 Forbidden*.
2. Memalsukan eksistensi ekstensi (`malware.exe` diganti jadi `malware.pdf`) ditangkal telak secara native oleh pembaca *mimetypes* Laravel.
3. Payload di luar standar array `DocumentType` tidak mengubah skema.

## 9. Filesystem Validation & Atomicity
`ReportService::createReport` kini memantau jejak file yang dieksekusi dengan *path tracking*. Segala *Exceptions* di pertengahan baris yang memicu *rollback* *Database*, akan otomatis disusul oleh penghapusan `Storage::disk('public')->delete($path)` untuk membersihkan serpihan file gagal secara komprehensif.

## 10. Automated Test
Total: 149
Passed: 149
Failed: 0
Skipped: 0
Assertions: 371

## 11. Regression Test
Previous: 143 tests / 356 assertions
Current: 149 tests / 371 assertions
Failed: 0

## 12. Database Integrity
Migration: NO
Schema: NO

## 13. UI Regression
Semua tampilan tetap sejalan dengan layout *Responsive* (*Mobile-first*) menggunakan *TailwindCSS*. Sidebar dan form tidak mendobrak *viewport*.

## 14. Issues Found
CRITICAL: Tidak ada.
IMPORTANT: Penanganan Cleanup *Atomicity* file telah diselesaikan.
OPTIONAL: Tidak ada.

## 15. Business Decision Required
- Fungsionalitas Dokumen Opsional berjalan sesuai dengan Business Rules.
- Desain antarmuka (Tanpa library ekstensi eksternal) berhasil memenuhi target fungsional minimal.

## 16. Final Verdict
UX Unggah Dokumen disempurnakan dengan performa cemerlang melalui pendekatan Vanilla JS. Skema *Backend Database Transaction* terbukti tangguh melindungi kebocoran *filesystem*.

DOCUMENT UPLOAD UX & VALIDATION — VALIDATED
READY FOR PHASE 7D

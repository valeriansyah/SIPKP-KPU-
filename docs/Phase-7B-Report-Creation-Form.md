# PHASE 7B — REPORT CREATION FORM UI & DECEASED DATA REVIEW

## 1. Audit Result
- **Existing**: `StoreReportRequest` pada Phase 7A sudah berjalan baik namun tidak memiliki relasi waktu khusus terkait batas `death_date` mendahului `birth_date`. Desain form di Phase 7A juga sudah `Single-Page` namun belum memanfaatkan *UI boundary* untuk `old()` parameter serta styling `@error` khas Laravel. 
- **Plan**: Melakukan refinemen CSS/Blade untuk menampilkan *error message* langsung di bawah setiap field, memberi border merah pada field yang `invalid`, mengunci field `Data Pelapor` murni dari Session, serta menambahkan validasi temporal ke sisi backend.

## 2. Files Created
*(Tidak ada file controller/model baru yang diciptakan, pembaruan sepenuhnya dilakukan di file eksisting)*

## 3. Files Modified
1. `resources/views/pelapor/laporan/create.blade.php` (Refinemen arsitektur Form UX)
2. `app/Http/Requests/StoreReportRequest.php` (Penambahan `after_or_equal:birth_date` dan custom error message)
3. `tests/Feature/ReportCreationFlowTest.php` (Penambahan test case untuk validasi gagal dan `death_date` invalid)

## 4. UI Architecture
Menggunakan arsitektur *Single-Page Vertical Form* dengan pembagian grid *Tailwind*:
1. **01 DATA PELAPOR**: Menampilkan nama, email, dan no HP (readonly/disabled) yang digali murni dari `Auth::user()`.
2. **02 DATA ALMARHUM**: Grid dinamis yang memetakan seluruh field esensial (NIK, No. KK, Nama, dsb). Dilengkapi *error states*.
3. **03 DOKUMEN PENDUKUNG**: Menampilkan daftar dokumen yang dibutuhkan lengkap dengan label "opsional/wajib", tapi UI dibatasi hanya *basic input* dengan informasi bahwa unggah dokumen penuh/penyempurnaan dilakukan pada Phase 7C.

## 5. Form Fields
- `reporter_name`, `reporter_email`, `reporter_phone`: Readonly (dari session)
- `nik`: NIK Almarhum (Required, Max 16)
- `family_card_number`: Nomor Kartu Keluarga (Required, Max 16)
- `name`: Nama Lengkap Almarhum (Required)
- `gender`: Select Jenis Kelamin (Required)
- `birth_place`: Tempat Lahir (Required)
- `birth_date`: Tanggal Lahir (Required)
- `district_id`: Select Kabupaten/Kota (Required, Populated from master data)
- `death_place`: Tempat Meninggal (Optional)
- `death_date`: Tanggal Meninggal (Required)
- `address`: Alamat Lengkap (Required)
- `documents.*`: Input Files.

## 6. Validation Rules
Sama dengan Phase 7A, namun ditambah dengan:
- `death_date`: `required|date|after_or_equal:birth_date`
(Mencegah laporan almarhum meninggal sebelum kelahirannya).

## 7. Authorization Validation
Seluruh otorisasi yang sudah dikunci oleh `Gate::authorize('create-report')` di Phase 7A tidak disentuh dan divalidasi oleh Regression Test. Sub-operator dan Operator tetap ditolak masuk (403).

## 8. Security Validation
Identitas Pelapor `user_id` tidak pernah masuk ke payload HTML Form. `Auth::user()->id` digunakan 100% pada `ReportService`. Payload `district_id` diverifikasi menggunakan `exists:districts,id`.

## 9. Automated Test
Seluruh skenario telah tercakup:
Total: 143
Passed: 143
Failed: 0
Skipped: 0
Assertions: 356

## 10. Regression Test
Previous:
142 tests / 353 assertions

Current:
143 tests / 356 assertions

## 11. Database Integrity
Migration changed:
NO

Schema changed:
NO

Seeder changed:
NO

## 12. Dashboard Integrity
Dashboard modified:
NO

Unexpected visual changes:
NO

## 13. UI Preview
URL: `/pelapor/laporan/create`

## 14. Issues Found
Tidak ada satupun arsitektur yang bentrok. Semua terintegrasi pas dengan framework validation FormRequest dari Laravel.

## 15. Business Decision Required
(Resolved) — Input `<input type="file">` html dibiarkan berjalan untuk menjaga integritas backend yang wajib meminta dokumen. Visualisasi Drag-and-Drop diserahkan murni untuk fase selanjutnya (7C).

## 16. Final Verdict
Phase 7B berhasil merapikan UI dan UX Create Form menggunakan validasi *server-side* yang *bullet-proof* dan *accessible*, tanpa merusak *dashboard final* atau *schema database*. Aktor siap diarahkan ke Phase 7C (UI Dokumen).

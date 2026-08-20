# DATABASE ARCHITECTURE & MIGRATION PLANNING

# Sistem Informasi Pelaporan Kematian Pemilih (SIPKP)

**Versi** : 1.0\
**Status** : Draft untuk Review\
**Instansi** : Komisi Pemilihan Umum Provinsi Sumatera Selatan

------------------------------------------------------------------------

# 1. Tujuan

Dokumen ini menjadi acuan teknis untuk menerjemahkan ERD SIPKP ke dalam
struktur database yang akan diimplementasikan menggunakan Laravel
Migration.

Dokumen ini tidak mengubah Business Rule, Entity Discovery, Entity
Specification, maupun ERD yang telah disepakati.

Tujuan dokumen: - Menentukan arsitektur database. - Menentukan
dependency antar tabel. - Menentukan Primary Key dan Foreign Key. -
Menentukan perilaku Foreign Key. - Menentukan index dan unique
constraint. - Menentukan penggunaan Soft Delete dan timestamp. -
Menentukan aturan validasi pada Application Service. - Menentukan urutan
Laravel Migration.

------------------------------------------------------------------------

# 2. Database Architecture

Database SIPKP terdiri dari 11 entity.

## 2.1 Master Data

1.  `roles`
2.  `districts`
3.  `document_types`
4.  `report_statuses`

## 2.2 System Data

5.  `users`
6.  `otp_codes`
7.  `audit_logs`

## 2.3 Transaction Data

8.  `reports`
9.  `deceased`
10. `documents`
11. `report_verifications`

------------------------------------------------------------------------

# 3. Table Dependency

Dependency utama:

``` text
roles
  ↓
users
  ↓
reports
  ↓
deceased
  ↓
documents
  ↓
report_verifications
```

Relasi pendukung:

``` text
districts → users
districts → deceased
document_types → documents
report_statuses → reports
report_statuses → report_verifications
users → otp_codes
users → audit_logs
users → report_verifications
```

------------------------------------------------------------------------

# 4. Primary Key

Seluruh tabel menggunakan `id` sebagai Primary Key.

Spesifikasi: - Tipe: `bigint unsigned` - Auto Increment - Primary Key

Pada Laravel Migration dapat menggunakan:

``` php
$table->id();
```

------------------------------------------------------------------------

# 5. Foreign Key

  Tabel                  Field              Referensi
  ---------------------- ------------------ --------------------
  users                  role_id            roles.id
  users                  district_id        districts.id
  otp_codes              user_id            users.id
  audit_logs             user_id            users.id
  reports                user_id            users.id
  reports                report_status_id   report_statuses.id
  deceased               report_id          reports.id
  deceased               district_id        districts.id
  documents              report_id          reports.id
  documents              document_type_id   document_types.id
  report_verifications   report_id          reports.id
  report_verifications   user_id            users.id
  report_verifications   report_status_id   report_statuses.id

------------------------------------------------------------------------

# 6. Cardinality

  Relationship                          Cardinality
  ------------------------------------- -------------
  Role → User                           1 : N
  District → User                       1 : N
  District → Deceased                   1 : N
  Document Type → Document              1 : N
  Report Status → Report                1 : N
  Report Status → Report Verification   1 : N
  User → Report                         1 : N
  User → OTP Code                       1 : N
  User → Audit Log                      1 : N
  User → Report Verification            1 : N
  Report → Deceased                     1 : 1
  Report → Document                     1 : N
  Report → Report Verification          1 : N

`deceased.report_id` dibuat UNIQUE untuk menjaga cardinality
`Report : Deceased = 1 : 1`.

------------------------------------------------------------------------

# 7. Foreign Key Behavior

Foreign Key harus menjaga integritas data dan tidak menghapus histori
transaksi secara tidak sengaja.

Rekomendasi umum: - Relasi ke master data: `ON DELETE RESTRICT`. - User
→ Report: `ON DELETE RESTRICT`. - Report → Deceased:
`ON DELETE RESTRICT`. - Report → Documents: `ON DELETE RESTRICT`. -
Report → Report Verification: `ON DELETE RESTRICT`.

Untuk OTP dan audit log, penghapusan user tidak boleh menghapus histori
penting secara tidak terkendali melalui cascade.

Perilaku final Foreign Key harus diterapkan konsisten pada Laravel
Migration.

------------------------------------------------------------------------

# 8. Index Strategy

## 8.1 Users

-   `username` → UNIQUE
-   `email` → UNIQUE
-   `role_id` → INDEX
-   `district_id` → INDEX

## 8.2 Reports

-   `report_number` → UNIQUE
-   `user_id` → INDEX
-   `report_status_id` → INDEX

## 8.3 Deceased

-   `report_id` → UNIQUE
-   `district_id` → INDEX
-   `nik` → INDEX
-   `family_card_number` → INDEX

`nik` tidak dibuat UNIQUE karena laporan dengan NIK yang sama dapat
dibuat kembali apabila laporan sebelumnya ditolak.

## 8.4 Documents

-   `report_id` → INDEX
-   `document_type_id` → INDEX

## 8.5 Report Verification

-   `report_id` → INDEX
-   `user_id` → INDEX
-   `report_status_id` → INDEX

## 8.6 OTP Code

-   `user_id` → INDEX
-   `email` → INDEX
-   `purpose` → INDEX
-   `expired_at` → INDEX

------------------------------------------------------------------------

# 9. Unique Constraint

Unique digunakan pada: - `roles.role_name` - `districts.name` -
`districts.code` - `document_types.name` -
`report_statuses.status_name` - `users.username` - `users.email` -
`reports.report_number` - `deceased.report_id`

------------------------------------------------------------------------

# 10. Soft Delete

Entity yang menggunakan `deleted_at`: - `users` - `reports` -
`deceased` - `documents`

Entity histori append-only: - `audit_logs` - `report_verifications`

Keduanya tidak menggunakan Soft Delete.

------------------------------------------------------------------------

# 11. Timestamp

Entity normal menggunakan: - `created_at` - `updated_at`

Entity append-only menggunakan: - `created_at`

Entity append-only: - `audit_logs` - `report_verifications`

------------------------------------------------------------------------

# 12. Business Validation & Application Rules

Tidak seluruh aturan bisnis dapat diterapkan dengan database constraint.

## 12.1 Duplikasi NIK

`deceased.nik` tidak dibuat UNIQUE.

Sistem harus memeriksa apakah NIK telah memiliki laporan aktif.

``` text
NIK baru
   ↓
Periksa laporan aktif
   ↓
Diproses / Disetujui?
   ├── Ya → Tolak
   └── Tidak → Lanjut
```

Laporan yang berstatus `Ditolak` dapat dilaporkan kembali sesuai
Business Rule.

## 12.2 Satu Sub Operator Aktif per Kabupaten/Kota

Satu Kabupaten/Kota hanya memiliki satu akun Sub Operator aktif.

Validasi dilakukan pada Application Service/Validator berdasarkan: -
role Sub Operator - `district_id` - `is_active`

## 12.3 District User

`users.district_id` dapat NULL untuk aktor yang tidak terikat pada satu
Kabupaten/Kota.

Sub Operator wajib memiliki `district_id`.

Wilayah laporan ditentukan berdasarkan:

``` text
deceased.district_id
```

bukan berdasarkan `users.district_id`.

## 12.4 Status Laporan

Status: - Pending - Diproses - Perlu Perbaikan - Disetujui - Ditolak

Perubahan status dilakukan melalui proses aplikasi dan dicatat pada
`report_verifications`.

## 12.5 Report Verification

Setiap perubahan/verifikasi menghasilkan record baru pada
`report_verifications`.

Record histori bersifat append-only.

------------------------------------------------------------------------

# 13. Migration Order

Urutan migration:

``` text
01. roles
02. districts
03. document_types
04. report_statuses
05. users
06. otp_codes
07. audit_logs
08. reports
09. deceased
10. documents
11. report_verifications
```

Dependency: - `users` membutuhkan `roles` dan `districts`. - `otp_codes`
dan `audit_logs` membutuhkan `users`. - `reports` membutuhkan `users`
dan `report_statuses`. - `deceased` membutuhkan `reports` dan
`districts`. - `documents` membutuhkan `reports` dan `document_types`. -
`report_verifications` membutuhkan `reports`, `users`, dan
`report_statuses`.

------------------------------------------------------------------------

# 14. Mapping Entity ke Laravel Table

  Entity                Laravel Table
  --------------------- ------------------------
  Role                  `roles`
  District              `districts`
  Document Type         `document_types`
  Report Status         `report_statuses`
  User                  `users`
  OTP Code              `otp_codes`
  Audit Log             `audit_logs`
  Report                `reports`
  Deceased              `deceased`
  Document              `documents`
  Report Verification   `report_verifications`

------------------------------------------------------------------------

# 15. Prinsip Implementasi Migration

1.  Primary key menggunakan `$table->id()`.
2.  Foreign key harus kompatibel dengan Primary Key.
3.  Field wajib menggunakan `NOT NULL`.
4.  Field opsional menggunakan `nullable()`.
5.  Unique field menggunakan `unique()`.
6.  Field relasional dan field pencarian penting diberikan index.
7.  Entity yang membutuhkan Soft Delete menggunakan `softDeletes()`.
8.  Entity append-only tidak menggunakan `updated_at`.
9.  Migration tidak boleh menambahkan entity baru di luar Entity
    Specification tanpa revisi desain.

------------------------------------------------------------------------

# 16. Validasi Sebelum Migration

-   [ ] 11 entity sesuai Entity Specification.
-   [ ] Tidak ada entity tambahan.
-   [ ] Tidak ada entity yang hilang.
-   [ ] Seluruh Primary Key ditentukan.
-   [ ] Seluruh Foreign Key ditentukan.
-   [ ] Cardinality sesuai ERD.
-   [ ] `Report → Deceased` = 1:1.
-   [ ] `Report → Document` = 1:N.
-   [ ] `Report → Report Verification` = 1:N.
-   [ ] `District → Deceased` = 1:N.
-   [ ] `phone_number` = NOT NULL.
-   [ ] `birth_place` dan `birth_date` = NOT NULL.
-   [ ] `report_date` tidak digunakan.
-   [ ] `report_verifications` append-only.
-   [ ] Validasi duplikasi NIK berada pada Application Service.
-   [ ] Validasi satu Sub Operator aktif per wilayah berada pada
    Application Service.

------------------------------------------------------------------------

# 17. Kesimpulan

Database SIPKP menggunakan 11 entity dengan pemisahan Master Data,
System Data, dan Transaction Data.

ERD menjadi dasar struktur relasional, sedangkan dokumen ini menjadi
acuan teknis sebelum implementasi Laravel Migration.

Tidak ada perubahan terhadap Business Rule maupun Entity Specification.

------------------------------------------------------------------------

# 18. Tahap Selanjutnya

Setelah dokumen ini disetujui:

``` text
Database Architecture & Migration Planning
                ↓
        Laravel Migration
                ↓
        Eloquent Model
                ↓
      Seeder & Master Data
                ↓
          Authentication
```

Migration hanya boleh dibuat berdasarkan desain yang telah disetujui.

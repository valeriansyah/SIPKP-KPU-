# ENTITY SPECIFICATION
# Sistem Informasi Pelaporan Kematian Pemilih (SIPKP)

**Versi:** 1.2  
**Instansi:** Komisi Pemilihan Umum Provinsi Sumatera Selatan  
**Status:** APPROVED FOR ERD DESIGN

---

# 1. Pendahuluan

## 1.1 Tujuan

Entity Specification merupakan spesifikasi rinci dari entity yang telah diidentifikasi pada tahap Entity Discovery. Dokumen ini menjadi acuan dalam penyusunan ERD, Data Dictionary, Laravel Migration, dan Eloquent Model.

Dokumen ini disusun berdasarkan:

- Business Rule SIPKP
- Entity Discovery SIPKP
- Hasil Design Review terhadap Entity Specification versi sebelumnya

## 1.2 Daftar Entity

### Master Data

1. Role
2. District
3. Document Type
4. Report Status

### System Data

5. User
6. OTP Code
7. Audit Log

### Transaction Data

8. Report
9. Deceased
10. Document
11. Report Verification

Total: **11 Entity**

---

# 2. Standar Database

## 2.1 Primary Key

Seluruh primary key menggunakan:

`bigint unsigned`

dengan mekanisme auto increment sesuai standar Laravel.

## 2.2 Foreign Key

Seluruh foreign key yang mereferensikan primary key menggunakan:

`bigint unsigned`

Tipe foreign key harus kompatibel dengan primary key yang direferensikan.

## 2.3 Timestamp

Entity yang membutuhkan timestamp menggunakan:

- `created_at`
- `updated_at`

Entity yang bersifat append-only dapat menggunakan `created_at` tanpa `updated_at`.

## 2.4 Soft Delete

Soft delete digunakan pada entity yang membutuhkan pemeliharaan riwayat data dan tidak boleh hilang secara permanen melalui operasi penghapusan normal.

Entity yang menggunakan `deleted_at`:

- User
- Report
- Deceased
- Document

`Report Verification` tidak menggunakan soft delete karena merupakan histori append-only.

## 2.5 Penamaan

Nama tabel menggunakan bentuk plural snake_case.

Contoh:

- `roles`
- `districts`
- `document_types`
- `report_statuses`
- `users`
- `otp_codes`
- `audit_logs`
- `reports`
- `deceased`
- `documents`
- `report_verifications`

---

# 3. MASTER ENTITY

# 3.1 ENT-001 — Role

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-001 |
| Entity | Role |
| Table | `roles` |
| Kategori | Master |
| Status | Approved |

## Deskripsi

Menyimpan jenis hak akses pengguna sistem SIPKP.

Role yang digunakan:

- Pelapor
- Sub Operator Kabupaten/Kota
- Operator Provinsi

## Relationship

- Role `1:N` User

## Business Rule

- Setiap User memiliki satu Role.
- Satu Role dapat dimiliki banyak User.
- Role digunakan sebagai dasar Role Based Access Control (RBAC).
- Role tidak dibuat sebagai tabel terpisah untuk setiap jenis pengguna.

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| role_name | varchar(50) | | | Tidak | ✓ | | Nama role |
| description | varchar(255) | | | Ya | | NULL | Deskripsi role |
| created_at | timestamp | | | Tidak | | | Waktu dibuat |
| updated_at | timestamp | | | Tidak | | | Waktu diperbarui |

## Constraint

- `role_name` wajib unik.
- Role yang masih digunakan User tidak boleh dihapus sembarangan.

---

# 3.2 ENT-002 — District

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-002 |
| Entity | District |
| Table | `districts` |
| Kategori | Master |
| Status | Approved |

## Deskripsi

Menyimpan data Kabupaten/Kota di Provinsi Sumatera Selatan.

District digunakan untuk menentukan wilayah kerja Sub Operator dan wilayah Kabupaten/Kota yang terkait dengan User maupun Almarhum.

## Relationship

- District `1:N` User
- District `1:N` Deceased

## Business Rule

- Satu District dapat memiliki banyak User.
- Satu District dapat memiliki banyak Deceased.
- Distribusi laporan kepada Sub Operator didasarkan pada Kabupaten/Kota Almarhum.
- District merupakan master data.

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| name | varchar(100) | | | Tidak | ✓ | | Nama Kabupaten/Kota |
| code | varchar(20) | | | Tidak | ✓ | | Kode wilayah |
| created_at | timestamp | | | Tidak | | | Waktu dibuat |
| updated_at | timestamp | | | Tidak | | | Waktu diperbarui |

## Constraint

- `name` wajib unik.
- `code` wajib unik.
- District tidak boleh dihapus apabila masih direferensikan oleh data transaksi.

---

# 3.3 ENT-003 — Document Type

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-003 |
| Entity | Document Type |
| Table | `document_types` |
| Kategori | Master |
| Status | Approved |

## Deskripsi

Menyimpan jenis dokumen yang dapat diunggah pada laporan.

Contoh:

- Surat Keterangan Kematian
- KTP Almarhum
- Kartu Keluarga
- KTP Pelapor
- Surat Pengantar RT/RW
- Surat Visum
- Akta Kelahiran
- Foto Almarhum

## Relationship

- Document Type `1:N` Document

## Business Rule

Satu jenis dokumen dapat digunakan oleh banyak Document.

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| name | varchar(100) | | | Tidak | ✓ | | Nama jenis dokumen |
| description | varchar(255) | | | Ya | | NULL | Deskripsi |
| is_required | boolean | | | Tidak | | false | Menentukan kewajiban dokumen |
| created_at | timestamp | | | Tidak | | | Waktu dibuat |
| updated_at | timestamp | | | Tidak | | | Waktu diperbarui |

## Constraint

- `name` wajib unik.
- Dokumen yang bersifat wajib mengikuti Business Rule SIPKP.

---

# 3.4 ENT-004 — Report Status

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-004 |
| Entity | Report Status |
| Table | `report_statuses` |
| Kategori | Master |
| Status | Approved |

## Deskripsi

Menyimpan daftar status yang digunakan dalam siklus hidup laporan.

## Status

1. Pending
2. Diproses
3. Perlu Perbaikan
4. Disetujui
5. Ditolak

## Relationship

- Report Status `1:N` Report
- Report Status `1:N` Report Verification

## Business Rule

- Setiap Report memiliki satu status terakhir.
- Histori status disimpan melalui Report Verification.
- Status digunakan secara konsisten oleh seluruh proses sistem.
- Status master tidak digantikan oleh ENUM agar domain status tetap terpusat dan konsisten.

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| status_name | varchar(50) | | | Tidak | ✓ | | Nama status |
| description | varchar(255) | | | Ya | | NULL | Deskripsi |
| created_at | timestamp | | | Tidak | | | Waktu dibuat |
| updated_at | timestamp | | | Tidak | | | Waktu diperbarui |

## Constraint

- `status_name` wajib unik.
- Status yang masih digunakan tidak boleh dihapus sembarangan.

---

# 4. SYSTEM ENTITY

# 4.1 ENT-005 — User

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-005 |
| Entity | User |
| Table | `users` |
| Kategori | System |
| Status | Approved |

## Deskripsi

Menyimpan seluruh akun pengguna SIPKP.

Aktor:

- Pelapor
- Sub Operator Kabupaten/Kota
- Operator Provinsi

## Business Rule

- Seluruh jenis pengguna menggunakan satu entity User.
- User memiliki satu Role.
- User dapat memiliki District.
- Login menggunakan Email dan Password.
- Username digunakan sebagai identitas pengguna.
- Email harus diverifikasi sesuai mekanisme autentikasi.
- OTP digunakan untuk registrasi dan reset password.
- Password disimpan dalam bentuk hash.
- Operator Provinsi merupakan akun awal yang disediakan melalui mekanisme seeding.
- Sub Operator dibuat oleh Operator Provinsi.
- Foto profil bersifat opsional.
- Soft Delete digunakan untuk menjaga riwayat akun.

## Relationship

- User `N:1` Role
- User `N:1` District
- User `1:N` Report
- User `1:N` OTP Code
- User `1:N` Audit Log
- User `1:N` Report Verification

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| role_id | bigint unsigned | | ✓ | Tidak | | | Relasi ke Role |
| district_id | bigint unsigned | | ✓ | Ya | | NULL | Wilayah User |
| full_name | varchar(100) | | | Tidak | | | Nama pengguna |
| username | varchar(50) | | | Tidak | ✓ | | Username |
| email | varchar(100) | | | Tidak | ✓ | | Email login |
| email_verified_at | timestamp | | | Ya | | NULL | Waktu verifikasi |
| password | varchar(255) | | | Tidak | | | Password hash |
| phone_number | varchar(20) | | | Tidak | | | Nomor telepon |
| profile_picture | varchar(255) | | | Ya | | NULL | Path foto profil |
| is_active | boolean | | | Tidak | | true | Status akun |
| remember_token | varchar(100) | | | Ya | | NULL | Token Laravel |
| created_at | timestamp | | | Tidak | | | Waktu dibuat |
| updated_at | timestamp | | | Tidak | | | Waktu diperbarui |
| deleted_at | timestamp | | | Ya | | NULL | Soft Delete |

## Design Decision

`district_id` nullable karena Operator Provinsi memiliki cakupan tingkat provinsi dan tidak harus terikat pada satu Kabupaten/Kota.

---

# 4.2 ENT-006 — OTP Code

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-006 |
| Entity | OTP Code |
| Table | `otp_codes` |
| Kategori | System |
| Status | Approved |

## Deskripsi

Menyimpan kode OTP untuk proses autentikasi.

Digunakan untuk:

- Registrasi
- Reset Password

## Relationship

- OTP Code `N:1` User

## Business Rule

- OTP memiliki masa berlaku.
- OTP kedaluwarsa tidak dapat digunakan.
- OTP dapat dibuat kembali melalui mekanisme kirim ulang.
- OTP dibedakan berdasarkan tujuan penggunaannya.
- OTP registrasi dapat dibuat sebelum User selesai dibuat.

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| user_id | bigint unsigned | | ✓ | Ya | | NULL | User terkait jika sudah tersedia |
| email | varchar(100) | | | Tidak | | | Email tujuan OTP |
| otp | varchar(10) | | | Tidak | | | Kode OTP |
| purpose | varchar(30) | | | Tidak | | | registration/reset_password |
| expired_at | timestamp | | | Tidak | | | Waktu kedaluwarsa |
| verified_at | timestamp | | | Ya | | NULL | Waktu OTP digunakan |
| created_at | timestamp | | | Tidak | | | Waktu dibuat |
| updated_at | timestamp | | | Tidak | | | Waktu diperbarui |

## Constraint

- OTP yang telah digunakan tidak dapat digunakan kembali.
- OTP yang telah kedaluwarsa tidak dapat digunakan.
- `purpose` harus berasal dari nilai yang diperbolehkan sistem.

---

# 4.3 ENT-007 — Audit Log

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-007 |
| Entity | Audit Log |
| Table | `audit_logs` |
| Kategori | System |
| Status | Approved |

## Deskripsi

Menyimpan jejak aktivitas penting yang dilakukan pengguna.

## Contoh Aktivitas

- Login
- Logout
- Registrasi
- Reset Password
- Membuat Laporan
- Perubahan Laporan
- Verifikasi
- Update Profil

## Relationship

- Audit Log `N:1` User

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| user_id | bigint unsigned | | ✓ | Ya | | NULL | Pengguna yang melakukan aktivitas |
| activity | varchar(100) | | | Tidak | | | Jenis aktivitas |
| description | text | | | Ya | | NULL | Detail aktivitas |
| ip_address | varchar(45) | | | Ya | | NULL | IP pengguna |
| user_agent | text | | | Ya | | NULL | Informasi perangkat/browser |
| created_at | timestamp | | | Tidak | | | Waktu aktivitas |

## Design Decision

Audit Log bersifat append-only. Tidak diperlukan `updated_at` karena record audit tidak dimaksudkan untuk diperbarui.

`user_id` dapat NULL untuk aktivitas yang terjadi sebelum autentikasi, misalnya percobaan registrasi atau login yang gagal.

---

# 5. TRANSACTION ENTITY

# 5.1 ENT-008 — Report

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-008 |
| Entity | Report |
| Table | `reports` |
| Kategori | Transaction |
| Status | Approved |

## Deskripsi

Menyimpan informasi utama laporan kematian pemilih.

## Relationship

- Report `N:1` User
- Report `N:1` Report Status
- Report `1:1` Deceased
- Report `1:N` Document
- Report `1:N` Report Verification

## Business Rule

- Setiap laporan dibuat oleh satu User.
- Satu User dapat membuat banyak laporan.
- Setiap laporan memiliki satu status terakhir.
- Setiap laporan memiliki satu data Almarhum.
- Setiap laporan dapat memiliki banyak dokumen.
- Setiap laporan dapat memiliki banyak riwayat verifikasi.
- Nomor laporan harus unik.
- Status laporan mengikuti lima status yang ditetapkan.
- `created_at` digunakan sebagai waktu pembuatan laporan.

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| user_id | bigint unsigned | | ✓ | Tidak | | | Pelapor |
| report_status_id | bigint unsigned | | ✓ | Tidak | | | Status terakhir |
| report_number | varchar(50) | | | Tidak | ✓ | | Nomor laporan |
| created_at | timestamp | | | Tidak | | | Waktu pembuatan laporan |
| updated_at | timestamp | | | Tidak | | | Waktu diperbarui |
| deleted_at | timestamp | | | Ya | | NULL | Soft Delete |

## Constraint

- `report_number` wajib unik.
- `user_id` wajib mengacu pada User valid.
- `report_status_id` wajib mengacu pada Report Status valid.

---

# 5.2 ENT-009 — Deceased

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-009 |
| Entity | Deceased |
| Table | `deceased` |
| Kategori | Transaction |
| Status | Approved |

## Deskripsi

Menyimpan data pemilih yang dilaporkan meninggal dunia.

## Relationship

- Deceased `N:1` District
- Deceased `1:1` Report

## Business Rule

- Data Almarhum merupakan bagian inti laporan.
- Kabupaten/Kota Almarhum digunakan sebagai dasar routing laporan kepada Sub Operator.
- NIK dan Nomor KK digunakan sebagai data identitas utama.
- Tempat Lahir dan Tanggal Lahir wajib diisi.
- Data identitas harus dapat diverifikasi berdasarkan dokumen pendukung.

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| report_id | bigint unsigned | | ✓ | Tidak | ✓ | | Relasi satu-ke-satu ke Report |
| district_id | bigint unsigned | | ✓ | Tidak | | | Kabupaten/Kota Almarhum |
| nik | varchar(16) | | | Tidak | | | NIK Almarhum |
| family_card_number | varchar(16) | | | Tidak | | | Nomor KK |
| name | varchar(100) | | | Tidak | | | Nama Almarhum |
| gender | varchar(20) | | | Tidak | | | Jenis kelamin |
| birth_place | varchar(100) | | | Tidak | | | Tempat lahir |
| birth_date | date | | | Tidak | | | Tanggal lahir |
| address | text | | | Tidak | | | Alamat |
| death_place | varchar(255) | | | Ya | | NULL | Tempat meninggal |
| death_date | date | | | Tidak | | | Tanggal meninggal |
| created_at | timestamp | | | Tidak | | | Waktu dibuat |
| updated_at | timestamp | | | Tidak | | | Waktu diperbarui |
| deleted_at | timestamp | | | Ya | | NULL | Soft Delete |

## Constraint

- `report_id` wajib unik untuk menjamin satu Report memiliki satu Deceased.
- `district_id` wajib ada.
- `nik` tidak ditetapkan UNIQUE karena laporan dengan NIK yang sama dapat dibuat kembali apabila laporan sebelumnya ditolak.
- Pencegahan laporan aktif/ganda untuk NIK yang sama dilakukan pada Application Service berdasarkan status laporan yang sedang berjalan.
- NIK harus mengikuti format yang ditetapkan sistem.

## Catatan Wilayah

Kecamatan dan Kelurahan/Desa belum ditambahkan karena Entity Discovery yang disepakati hanya menetapkan master `District` (Kabupaten/Kota). Penambahan wilayah lebih detail memerlukan perubahan Business Rule dan Entity Discovery terlebih dahulu.

---

# 5.3 ENT-010 — Document

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-010 |
| Entity | Document |
| Table | `documents` |
| Kategori | Transaction |
| Status | Approved |

## Deskripsi

Menyimpan metadata dokumen pendukung yang diunggah untuk sebuah laporan.

File fisik tidak disimpan sebagai binary database. Entity ini menyimpan metadata dan lokasi file.

## Relationship

- Document `N:1` Report
- Document `N:1` Document Type

## Business Rule

- Satu Report dapat memiliki banyak Document.
- Setiap Document memiliki satu Document Type.
- Dokumen harus terkait dengan Report.
- Jenis dokumen harus berasal dari master Document Type.
- Dokumen harus melalui validasi file sebelum disimpan.

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| report_id | bigint unsigned | | ✓ | Tidak | | | Relasi laporan |
| document_type_id | bigint unsigned | | ✓ | Tidak | | | Jenis dokumen |
| file_name | varchar(255) | | | Tidak | | | Nama file |
| file_path | varchar(500) | | | Tidak | | | Lokasi file |
| mime_type | varchar(100) | | | Ya | | NULL | MIME type |
| file_size | bigint unsigned | | | Ya | | NULL | Ukuran file |
| created_at | timestamp | | | Tidak | | | Waktu metadata dibuat |
| updated_at | timestamp | | | Tidak | | | Waktu metadata diperbarui |
| deleted_at | timestamp | | | Ya | | NULL | Soft Delete |

## Design Decision

`created_at` digunakan sebagai waktu metadata dokumen dibuat/diunggah sehingga tidak diperlukan field `uploaded_at`.

File fisik menggunakan mekanisme storage yang ditentukan pada tahap implementasi.

---

# 5.4 ENT-011 — Report Verification

## Informasi Umum

| Atribut | Nilai |
|---|---|
| Entity ID | ENT-011 |
| Entity | Report Verification |
| Table | `report_verifications` |
| Kategori | Transaction |
| Status | Approved |

## Deskripsi

Menyimpan histori proses dan aksi perubahan status laporan yang dilakukan oleh User yang memiliki kewenangan.

## Relationship

- Report Verification `N:1` Report
- Report Verification `N:1` User
- Report Verification `N:1` Report Status

## Business Rule

- Setiap proses verifikasi menghasilkan histori.
- User yang melakukan aksi harus tercatat.
- Status hasil aksi harus tercatat.
- Catatan dapat disimpan.
- Histori sebelumnya tidak boleh dihilangkan.
- Status terkini Report diperbarui berdasarkan proses yang valid.
- Pelapor yang melakukan perbaikan laporan juga dapat tercatat sebagai User pada histori apabila proses tersebut menghasilkan perubahan status.

## Field Specification

| Field | Type | PK | FK | Null | Unique | Default | Keterangan |
|---|---|---|---|---|---|---|---|
| id | bigint unsigned | ✓ | | Tidak | ✓ | Auto Increment | Primary Key |
| report_id | bigint unsigned | | ✓ | Tidak | | | Laporan yang diproses |
| user_id | bigint unsigned | | ✓ | Tidak | | | User yang melakukan aksi |
| report_status_id | bigint unsigned | | ✓ | Tidak | | | Status hasil aksi |
| notes | text | | | Ya | | NULL | Catatan proses |
| created_at | timestamp | | | Tidak | | | Waktu aksi |

## Design Decision

Field `verifier_user_id` diganti menjadi `user_id` agar entity dapat mencatat seluruh User yang melakukan aksi pada histori, baik petugas verifikasi maupun Pelapor yang melakukan perbaikan sesuai kewenangannya.

`created_at` digunakan sebagai waktu aksi sehingga tidak diperlukan field `verified_at`.

`Report Verification` bersifat append-only dan tidak menggunakan `updated_at` maupun `deleted_at`. Histori tidak boleh dihapus atau diubah melalui operasi normal.

---

# 6. RELATIONSHIP SUMMARY

| Parent | Child | Cardinality | Foreign Key |
|---|---|---|---|
| Role | User | 1:N | `users.role_id` |
| District | User | 1:N | `users.district_id` |
| District | Deceased | 1:N | `deceased.district_id` |
| Document Type | Document | 1:N | `documents.document_type_id` |
| Report Status | Report | 1:N | `reports.report_status_id` |
| Report Status | Report Verification | 1:N | `report_verifications.report_status_id` |
| User | Report | 1:N | `reports.user_id` |
| User | OTP Code | 1:N | `otp_codes.user_id` |
| User | Audit Log | 1:N | `audit_logs.user_id` |
| User | Report Verification | 1:N | `report_verifications.user_id` |
| Report | Deceased | 1:1 | `deceased.report_id` |
| Report | Document | 1:N | `documents.report_id` |
| Report | Report Verification | 1:N | `report_verifications.report_id` |

---

# 7. DATABASE CONSTRAINT SUMMARY

## Primary Key

Seluruh entity memiliki `id` sebagai primary key dengan tipe `bigint unsigned`.

## Foreign Key

Seluruh foreign key menggunakan `bigint unsigned` dan harus kompatibel dengan primary key parent.

## Unique

Field yang ditetapkan unik:

- `roles.role_name`
- `districts.name`
- `districts.code`
- `document_types.name`
- `report_statuses.status_name`
- `users.username`
- `users.email`
- `reports.report_number`
- `deceased.report_id`

NIK Almarhum **tidak** ditetapkan UNIQUE.

## Referential Integrity

Penghapusan master data yang masih direferensikan transaksi harus dicegah atau ditangani secara eksplisit.

## Duplicate Active Report

Duplikasi laporan berdasarkan NIK ditangani melalui Application Service.

Sistem harus memeriksa laporan yang sedang aktif sebelum membuat laporan baru untuk NIK yang sama.

Laporan yang sudah Ditolak dapat dilaporkan kembali sesuai Business Rule.

## Soft Delete

Soft delete diterapkan pada:

- User
- Report
- Deceased
- Document

Report Verification bersifat append-only dan tidak menggunakan soft delete.

---

# 8. DESIGN DECISION SUMMARY

## 8.1 Satu Tabel User

Pelapor, Sub Operator, dan Operator Provinsi menggunakan entity `User` yang sama dan dibedakan melalui `Role`.

## 8.2 District pada User dan Deceased

- Pelapor dapat memiliki `district_id = NULL`.
- Sub Operator wajib memiliki `district_id`.
- Operator Provinsi memiliki `district_id = NULL`.
- Wilayah laporan tidak ditentukan berdasarkan `users.district_id`.
- Wilayah laporan ditentukan berdasarkan `deceased.district_id`.
- `deceased.district_id` digunakan untuk menentukan Kabupaten/Kota tujuan verifikasi.

## 8.3 Report Status sebagai Master

`Report Status` dipertahankan sebagai entity master.

Status terakhir disimpan pada `Report`, sedangkan histori perubahan status disimpan pada `Report Verification`.

## 8.4 Deceased Terpisah dari Report

Pemisahan dilakukan agar data transaksi laporan dan data individu Almarhum memiliki struktur yang jelas dan mendukung normalisasi.

## 8.5 Document Terpisah

Satu Report dapat memiliki banyak dokumen sehingga dokumen tidak disimpan sebagai banyak kolom pada Report.

## 8.6 OTP Mendukung Registrasi dan Reset Password

Entity OTP digunakan untuk kedua proses tersebut dan dibedakan melalui `purpose`.

## 8.7 OTP User Nullable

OTP registrasi dapat dibuat sebelum User selesai dibuat sehingga `user_id` dapat bernilai NULL.

## 8.8 Audit Log Terpisah

Audit Log tidak digabungkan dengan User atau Report karena satu User dapat menghasilkan banyak aktivitas.

## 8.9 Report Verification sebagai Histori

Report Verification menyimpan histori aksi/status dan tidak menggantikan status terakhir pada Report.

---

# 9. INDEXING RECOMMENDATION

Index tambahan dapat diterapkan pada field yang sering digunakan untuk pencarian, filtering, routing, dan dashboard.

Rekomendasi awal:

- `users.role_id`
- `users.district_id`
- `otp_codes.email`
- `otp_codes.purpose`
- `otp_codes.expired_at`
- `reports.user_id`
- `reports.report_status_id`
- `deceased.district_id`
- `deceased.nik`
- `documents.report_id`
- `documents.document_type_id`
- `report_verifications.report_id`
- `report_verifications.user_id`
- `report_verifications.report_status_id`

Index final ditentukan setelah kebutuhan query dan ERD direview.

---

# 10. VALIDATION & APPLICATION RULES

Beberapa aturan tidak cukup ditangani oleh database constraint dan harus diterapkan pada Application Service/Service Layer.

## 10.1 Duplicate Active Report

Sebelum laporan dibuat, sistem memeriksa apakah NIK Almarhum telah memiliki laporan aktif yang masih berada pada status proses.

## 10.2 Document Validation

Sistem memvalidasi:

- jenis file
- ukuran file
- jenis dokumen
- keterkaitan dokumen dengan Report
- kewajiban dokumen

## 10.3 Status Transition

Perubahan status harus mengikuti alur bisnis SIPKP dan kewenangan role.

## 10.4 Authorization

User hanya dapat melakukan operasi sesuai Role dan wilayah kewenangannya.

## 10.5 OTP Validation

OTP harus:

- sesuai tujuan penggunaan
- belum kedaluwarsa
- belum digunakan
- sesuai email/User yang terkait

## 10.6 Sub Operator Account Limit

- Satu Kabupaten/Kota hanya dapat memiliki satu akun Sub Operator aktif.
- Validasi dilakukan pada Application Service/Service Layer.
- Sebelum membuat atau mengaktifkan akun Sub Operator, sistem harus memeriksa apakah sudah terdapat Sub Operator aktif pada `district_id` yang sama.
- Jika sudah terdapat Sub Operator aktif, sistem menolak pembuatan atau aktivasi akun Sub Operator baru untuk wilayah tersebut.
- Constraint ini tidak dipaksakan melalui UNIQUE sederhana karena akun yang tidak aktif masih dapat dipertahankan sebagai histori.

---

# 11. OUT OF SCOPE ENTITY

Entity berikut tidak digunakan pada desain saat ini karena belum diperlukan oleh Business Rule:

- Notification
- Session
- Email Log
- File Storage
- Reporter
- Sub Operator sebagai tabel terpisah

Alasan:

Kebutuhan telah diakomodasi oleh entity yang telah ditentukan dan penambahan entity tersebut akan meningkatkan kompleksitas tanpa kebutuhan bisnis yang telah disepakati.

---

# 12. FINAL RELATIONSHIP MAP

```text
Role
 │
 └──< User >── District
       │
       ├──< OTP Code
       ├──< Audit Log
       ├──< Report
       │      │
       │      ├── Report Status
       │      ├── Deceased >── District
       │      ├──< Document >── Document Type
       │      └──< Report Verification >── User
       │                                  │
       │                                  └── Report Status
       │
       └──< Report Verification
```

---

# 13. STATUS DOKUMEN

**Entity Specification Version 1.2**

Status:

**APPROVED FOR ERD DESIGN**

Dokumen ini telah:

- mempertahankan 11 Entity dari Entity Discovery;
- menyesuaikan field dengan Business Rule;
- menambahkan `profile_picture`;
- memperbaiki kewajiban `birth_place` dan `birth_date`;
- menetapkan PK/FK sebagai `bigint unsigned`;
- mempertahankan `Report Status` sebagai master;
- menetapkan aturan pencegahan laporan aktif/ganda berdasarkan NIK;
- memperjelas `Report Verification` sebagai histori append-only;
- menghapus field waktu yang redundan;
- memperjelas aturan kewilayahan Sub Operator dan Laporan;
- memperjelas relationship dan foreign key;
- menambahkan rekomendasi indexing;
- memisahkan aturan database dengan aturan Application Service.

Dokumen ini menjadi dasar untuk tahap berikutnya:

**03. ERD (Entity Relationship Diagram)**

Tahap selanjutnya adalah menerjemahkan Entity Specification ini ke dalam ERD tanpa mengubah entity, relationship, atau business rule yang telah disetujui tanpa review tambahan.

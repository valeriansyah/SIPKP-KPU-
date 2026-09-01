# HANDOVER SIPKP

## 1. Tentang SIPKP

SIPKP (Sistem Informasi Pelaporan Kematian Pemilih) merupakan aplikasi berbasis web yang dirancang dan dibangun untuk membantu proses pelaporan kematian pemilih pada KPU Provinsi Sumatera Selatan.

Project ini dikembangkan sebagai bagian dari kegiatan Kerja Praktik dan diserahkan kepada pegawai KPU Provinsi Sumatera Selatan untuk dapat dipelajari, dikembangkan lebih lanjut, dan apabila diperlukan diimplementasikan pada lingkungan server internal KPU.

---

## 2. Tujuan Sistem

SIPKP dirancang untuk:

- memfasilitasi masyarakat/pelapor dalam menyampaikan laporan kematian pemilih;
- memfasilitasi Sub Operator dalam melakukan verifikasi laporan berdasarkan wilayah Kabupaten/Kota;
- memfasilitasi Operator Provinsi dalam melakukan monitoring laporan secara global;
- menjaga dokumen laporan tetap berada pada penyimpanan privat;
- menyediakan workflow perbaikan laporan yang terarah;
- mendukung notifikasi email untuk perubahan status tertentu.

---

## 3. Teknologi

Teknologi utama yang digunakan dalam project:

- Laravel
- PHP >= 8.3
- Blade Template
- Vite
- JavaScript
- SQLite untuk environment pengembangan
- Git / GitHub untuk version control

### Database Pengembangan

Database yang digunakan selama proses pengembangan dan pengujian adalah:

**SQLite**

SQLite dipilih karena sederhana, mudah dipindahkan, dan tidak membutuhkan database server terpisah pada tahap pengembangan.

### Database Production

Database production **belum ditentukan**.

Apabila sistem nantinya akan digunakan secara resmi pada server KPU, tim internal dapat:

- tetap menggunakan SQLite apabila skala penggunaan dan kebijakan infrastruktur memungkinkan; atau
- melakukan konfigurasi/migrasi ke MySQL/MariaDB apabila membutuhkan database server untuk kebutuhan operasional multi-user yang lebih besar.

Keputusan akhir mengikuti kebijakan dan infrastruktur KPU Provinsi Sumatera Selatan.

---

## 4. Role Sistem

### 4.1 Pelapor

Pelapor memiliki akses untuk:

- login ke sistem;
- menggunakan Google OAuth jika dikonfigurasi;
- membuat laporan kematian pemilih;
- mengunggah dokumen pendukung;
- melihat daftar dan detail laporan miliknya;
- melakukan perbaikan laporan ketika status `perlu_perbaikan`;
- mengunggah ulang dokumen yang secara spesifik diminta untuk diperbaiki;
- mengelola profil dan foto profil;
- melihat informasi kontak Sub Operator sesuai Kabupaten/Kota.

### 4.2 Sub Operator

Sub Operator memiliki akses terbatas berdasarkan wilayah Kabupaten/Kota.

Fitur utama:

- melihat antrean laporan pada wilayahnya;
- melihat detail laporan;
- melihat dokumen laporan;
- melakukan verifikasi laporan;
- memberikan keputusan sesuai workflow sistem;
- memilih field data dan/atau dokumen tertentu yang harus diperbaiki;
- memberikan catatan perbaikan.

Sub Operator tidak boleh mengakses laporan dari district/wilayah lain.

### 4.3 Operator Provinsi

Operator Provinsi memiliki akses global sesuai hak akses sistem.

Fitur utama:

- monitoring seluruh laporan;
- filter berdasarkan Kabupaten/Kota;
- pencarian laporan;
- filter status;
- melihat detail laporan;
- mengelola master data;
- mengelola akun Sub Operator sesuai fitur yang tersedia.

---

## 5. Fitur yang Telah Tersedia

Fitur utama yang telah selesai dikembangkan antara lain:

- authentication;
- Google OAuth;
- dashboard sesuai role;
- pembuatan laporan kematian pemilih;
- upload dokumen privat;
- verifikasi laporan;
- targeted revision workflow;
- kontak Sub Operator berdasarkan district;
- district profile Pelapor;
- email notification;
- document preparation checklist modal;
- sticky stepper pada form laporan;
- filter laporan Operator Provinsi;
- master data;
- RBAC;
- foto profil.

---

## 6. Workflow Sistem

### 6.1 Workflow Pelaporan

Pelapor:

1. Login.
2. Membuat laporan baru.
3. Mengisi data Pelapor dan data pemilih yang meninggal.
4. Mengunggah dokumen wajib.
5. Mengirim laporan.
6. Menunggu proses verifikasi.

### 6.2 Workflow Verifikasi

Sub Operator:

1. Membuka antrean laporan di wilayahnya.
2. Membuka detail laporan.
3. Memeriksa data dan dokumen.
4. Memberikan keputusan verifikasi.

### 6.3 Workflow Perlu Perbaikan

Jika laporan membutuhkan perbaikan:

1. Sub Operator memilih status `perlu_perbaikan`.
2. Sub Operator memilih data tertentu yang perlu diperbaiki.
3. Sub Operator dapat memilih dokumen tertentu yang perlu diunggah ulang.
4. Sub Operator memberikan catatan.
5. Pelapor menerima informasi bahwa laporan membutuhkan perbaikan.
6. Pelapor hanya perlu memperbaiki bagian yang ditandai.
7. Pelapor mengunggah ulang dokumen yang diminta.
8. Pelapor mengirim perbaikan.
9. Status kembali ke `pending`.
10. Laporan dapat diverifikasi kembali oleh Sub Operator.

### 6.4 Workflow Operator Provinsi

Operator Provinsi dapat:

- melihat laporan seluruh wilayah;
- melakukan pencarian;
- melakukan filter Kabupaten/Kota;
- melakukan filter status;
- melakukan monitoring data;
- mengelola master data sesuai fitur aplikasi.

---

## 7. Struktur Project

Struktur utama project:

```text
app/
config/
database/
resources/
routes/
storage/
tests/
docs/
```

Keterangan:

- `app/` berisi controller, model, service, request validation, notification, dan logic aplikasi.
- `config/` berisi konfigurasi Laravel.
- `database/` berisi migration, factory, dan seeder.
- `resources/` berisi Blade view serta asset frontend.
- `routes/` berisi route aplikasi.
- `storage/` berisi cache, log, file publik tertentu, dan dokumen privat.
- `tests/` berisi automated tests.
- `docs/` berisi dokumentasi project.

---

## 8. Struktur Database

Tabel utama antara lain:

- `users`
- `roles`
- `districts`
- `reports`
- `deceased`
- `documents`
- `document_types`
- `report_statuses`
- `verifications`
- `report_revision_items`

### report_revision_items

Tabel ini digunakan untuk menyimpan bagian laporan yang secara spesifik diminta untuk diperbaiki.

Revision item dapat berupa:

- field data;
- dokumen.

---

## 9. Instalasi untuk Development

### 9.1 Clone Repository

```bash
git clone <repository-url>
cd <project-directory>
```

### 9.2 Install Dependency PHP

```bash
composer install
```

### 9.3 Install Dependency Frontend

```bash
npm install
```

### 9.4 Siapkan Environment

Windows:

```bash
copy .env.example .env
```

Linux/macOS:

```bash
cp .env.example .env
```

### 9.5 Generate APP_KEY

```bash
php artisan key:generate
```

### 9.6 Konfigurasi SQLite

Pastikan file database SQLite tersedia sesuai konfigurasi project.

Apabila diperlukan, buat file database:

```text
database/database.sqlite
```

Sesuaikan `.env` development dengan konfigurasi SQLite project.

Contoh umum:

```env
DB_CONNECTION=sqlite
```

Jangan memasukkan database development ke repository apabila sudah dikecualikan melalui `.gitignore`.

### 9.7 Jalankan Migration

```bash
php artisan migrate
```

Jika project membutuhkan data awal:

```bash
php artisan db:seed
```

### 9.8 Storage Link

Untuk foto profil:

```bash
php artisan storage:link
```

### 9.9 Build Asset

```bash
npm run build
```

Untuk development frontend dapat menggunakan:

```bash
npm run dev
```

### 9.10 Menjalankan Aplikasi

```bash
php artisan serve
```

Perintah ini untuk development lokal, bukan untuk production.

---

## 10. Environment Configuration

Variable penting antara lain:

```text
APP_*
DB_*
MAIL_*
GOOGLE_*
SESSION_*
```

Credential asli tidak boleh dimasukkan ke repository.

### Production

Jika nanti akan digunakan pada production, minimal:

```env
APP_ENV=production
APP_DEBUG=false
```

Konfigurasi final production harus ditentukan oleh tim internal KPU.

---

## 11. Storage

### Profile Photo

Foto profil menggunakan public storage.

Lokasi mengikuti konfigurasi `public` disk Laravel dan memerlukan:

```bash
php artisan storage:link
```

### Report Document

Dokumen laporan menggunakan private storage.

Dokumen laporan **tidak boleh dipindahkan langsung ke public storage**.

Akses file harus tetap melalui mekanisme aplikasi dan authorization.

---

## 12. Email Notification

Sistem memiliki notifikasi email untuk beberapa perubahan status, antara lain:

- `perlu_perbaikan`;
- `disetujui`.

Email dikirim oleh sistem melalui konfigurasi SMTP.

Credential email disimpan melalui environment variable dan tidak boleh dimasukkan ke source code maupun GitHub.

---

## 13. Google OAuth

Sistem mendukung Google OAuth.

Callback route:

```text
/auth/google/callback
```

Environment yang berkaitan dengan Google OAuth harus dikonfigurasi sesuai project, seperti:

```text
GOOGLE_CLIENT_ID
GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URI
```

Apabila domain berubah, redirect URI pada Google Cloud Console juga harus disesuaikan.

Contoh production:

```text
https://DOMAIN-KPU/auth/google/callback
```

---

## 14. Testing

Automated test dapat dijalankan dengan:

```bash
php artisan test
```

Build frontend:

```bash
npm run build
```

Baseline terakhir pada saat serah terima:

- Tests: 191
- Assertions: 566
- Failed: 0

Jumlah dapat berubah apabila project dikembangkan lebih lanjut.

---

## 15. Development Guidelines

Beberapa aturan yang disarankan:

- Jangan mengubah migration lama yang sudah pernah digunakan.
- Gunakan migration baru untuk perubahan database.
- Jangan commit file `.env`.
- Jangan commit database development.
- Jangan commit dokumen laporan privat.
- Jangan commit credential API, SMTP, atau Google OAuth.
- Jalankan `php artisan test` sebelum commit.
- Jalankan `npm run build` sebelum release/checkpoint penting.
- Gunakan feature branch untuk perubahan besar.
- Gunakan `git pull` atau `git fetch` sebelum mulai pekerjaan agar selaras dengan `origin/main`.

---

## 16. Future Deployment Notes

**Catatan Deployment — bukan proses deployment aktual.**

Pada tahap pengembangan dan Kerja Praktik, SIPKP menggunakan database **SQLite**.

Jika sistem nantinya akan diimplementasikan pada server KPU Provinsi Sumatera Selatan, tim internal KPU dapat:

- tetap menggunakan SQLite apabila skala penggunaan dan kebijakan infrastruktur memungkinkan; atau
- melakukan konfigurasi/migrasi ke database server seperti MySQL/MariaDB apabila dibutuhkan untuk kebutuhan operasional.

Tim internal perlu mempertimbangkan:

- Linux/Windows server sesuai kebijakan KPU;
- PHP >= 8.3;
- database sesuai kebijakan deployment:
  - SQLite; atau
  - MySQL/MariaDB;
- Nginx/Apache/IIS sesuai lingkungan server;
- domain/subdomain resmi KPU;
- HTTPS;
- SMTP resmi institusi;
- Google OAuth production;
- backup database;
- backup dokumen privat;
- konfigurasi `APP_ENV=production`;
- konfigurasi `APP_DEBUG=false`.

Perubahan database engine dilakukan melalui konfigurasi environment dan harus diuji ulang terhadap migration, query, test, dan seluruh workflow aplikasi.

---

## 17. Known Future Work

Pengembangan lanjutan yang dapat dilakukan oleh tim internal:

- deployment ke server internal;
- production SMTP configuration;
- production Google OAuth integration;
- audit keamanan lanjutan;
- monitoring/logging terpusat;
- backup automation database sesuai engine yang digunakan;
- backup file SQLite apabila production tetap memakai SQLite;
- scheduled database dump apabila menggunakan MySQL/MariaDB;
- backup dokumen privat;
- UI/UX refinement;
- reporting/export Excel/PDF apabila diperlukan;
- peningkatan audit log apabila dibutuhkan;
- integrasi dengan sistem internal lainnya apabila disetujui.

---

## 18. Git Workflow

Branch utama project adalah:

```text
main
```

Tim internal disarankan menggunakan feature branch untuk pengembangan.

Contoh:

```text
main
├── feature/nama-fitur
├── fix/nama-bug
└── refactor/nama-perubahan
```

Alur sederhana:

1. `git fetch`
2. pastikan local branch terbaru;
3. buat feature branch;
4. lakukan perubahan;
5. jalankan test;
6. commit;
7. review;
8. merge ke `main`.

Hindari force push pada `main`.

---

## 19. Handover Checklist

- [x] Source code tersedia di repository.
- [x] Branch utama menggunakan `main`.
- [x] Environment template tersedia tanpa secrets.
- [x] Database development menggunakan SQLite.
- [x] Private document storage terdokumentasi.
- [x] Role dan workflow terdokumentasi.
- [x] Workflow Perlu Perbaikan terdokumentasi.
- [x] Filter Operator Provinsi terdokumentasi.
- [x] Email notification terdokumentasi.
- [x] Google OAuth terdokumentasi.
- [x] Testing guide tersedia.
- [x] Future deployment notes tersedia.
- [x] Credential production tidak disertakan.
- [x] Project dapat dilanjutkan oleh tim internal KPU.

---

## Penutup

Project SIPKP pada tahap serah terima merupakan hasil perancangan dan pembangunan sistem selama kegiatan Kerja Praktik.

Source code, struktur sistem, workflow utama, serta dokumentasi development disiapkan agar dapat menjadi dasar bagi KPU Provinsi Sumatera Selatan untuk melakukan pengembangan lebih lanjut sesuai kebutuhan organisasi, kebijakan keamanan, serta infrastruktur internal yang tersedia.

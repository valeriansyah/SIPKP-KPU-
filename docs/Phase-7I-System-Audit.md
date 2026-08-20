# Phase 7I — System Audit Report

## A. Authentication Pelapor
- **Status:** Diimplementasikan dengan baik.
- **Observasi:** 
  - Login via Google berhasil mengumpulkan data `name` dan `email`.
  - Jika belum memiliki akun, sistem membuat akun baru (contoh riil: ID 24, Name: Valerian, Username: `pelapor_gvx4q4`).
  - *Password* baru digenerate secara acak dengan Hash Laravel dan tidak bisa dibaca kembali.
  - Penamaan otomatis `pelapor_gvx4q4` mungkin terasa kurang lazim bagi Pelapor. Mereka bisa mengubahnya melalui laman Profil.
  - Alur registrasi mulus tanpa celah *dummy password* di antarmuka.

## B. Login Universal
- **Status:** Perlu peninjauan UI.
- **Observasi:** Sistem menentukan `role` secara otomatis di belakang layar. Halaman masuk tidak lagi memaksa pemilihan *role*. Terdapat GAP pada _wording_ atau kalimat sambutan di antarmuka _login_ yang mungkin masih terasa kaku.

## C. Database Pelapor
- **Status:** Terisolasi & Aman.
- **Observasi:**
  - Akun pelapor "pelapor_test" tidak diproduksi otomatis di ekosistem nyata.
  - Semua pengguna menggunakan `users.id` yang diikat secara sah melalui OAuth atau akun *default* seeder (untuk Sub-Operator dan Operator).

## D. Profile Pelapor
- **Status:** Fungsional dengan potensi UI GAP.
- **Observasi:**
  - Profil menyediakan fungsi pembaruan atribut seperti nama, nomor HP, dan _password_.
  - Penyimpanan foto profil perlu diuji kembali kompatibilitas pengunggahan *multipart* dan visualisasinya di peramban.

## E. Report Creation
- **Status:** Diimplementasikan sepenuhnya secara logis.
- **Observasi:** Pembuatan laporan, dan relasinya dengan data jenazah (`Deceased`) serta log aktivitas (`AuditLog`) telah terbungkus dalam `DB::transaction()`. Tidak ada entri data yang disimulasikan.

## F. Document Upload
- **Status:** GAP UI (Front-End).
- **Observasi:**
  - *Backend* menerima dan memvalidasi berkas (MIMES, Max Size) dengan sempurna.
  - *Metadata* dokumen tercatat dengan sukses pada tabel `documents`.
  - **Identifikasi Masalah:** Nama file yang dipilih terkadang tidak muncul di layar. Ini dikonfirmasi sebagai masalah *JavaScript/Alpine.js/Blade component* di mana DOM tidak di-update setelah _trigger event_ `change` pada *input file*.

## G. Sub-Operator
- **Status:** Bebas _Hardcode_, 17 Wilayah Lengkap.
- **Observasi:** Ke-17 Kabupaten/Kota telah memiliki akun spesifik dan diikat langsung dengan `district_id` yang sesuai. Sistem isolasi bekerja sebagaimana mestinya (Sub-Operator Palembang tidak dapat melihat laporan Sub-Operator Lahat).

## H. Verification Workflow
- **Status:** Tersedia parsial.
- **Observasi:** Alur *Terima / Tolak / Perlu Perbaikan* di dasbor Sub-Operator memerlukan konfirmasi UI untuk aksi. Formulir status sudah ada di `ReportService::updateReport` namun pengalaman eksekusi (UX) di layar Sub-Operator membutuhkan umpan balik yang lebih *professional*.

## I. Operator Provinsi
- **Status:** GAP UI Analitik.
- **Observasi:** Operator hanya memiliki 1 akun dan berhasil memonitor ke-17 wilayah. Sayangnya Dasbor belum menampilkan visualisasi statistik/grafik untuk data "Pending, Disetujui, Ditolak, Perlu Perbaikan".

## J. Navigation
- **Status:** Cukup fungsional, berpotensi memiliki celah pada penempatan navigasi *mobile*.
- **Observasi:** Menu keluar (Logout) serta profil berjalan sebagaimana tautan aktif, tidak lagi mengandalkan tag kosong `href="#"`.

## K. UI/UX Audit
- **Status:** Perlu perbaikan identitas aktor.
- **Observasi:** Estetika antarmuka ketiga aktor perlu dipisahkan lebih kuat. Pelapor membutuhkan desain inklusif layaknya Portal Layanan Masyarakat, sedangkan Operator & Sub-Operator membutuhkan gaya Administratif Internal.

## L. Database Integrity
- **Status:** Aman & Terintegrasi Penuh.
- **Observasi:** *Foreign Key constraints* terpasang kuat, menjaga konsistensi data antara `reports`, `users`, `districts`, `deceased`, `documents`, dan `audit_logs`. Tidak ditemukan catatan *orphan* pada *database* nyata.

## M. Testing
- **Status:** GAP pada cakupan End-to-End.
- **Observasi:** Pengujian yang ada sudah mencakup aspek keamanan rute (OAuth, Verifikasi Konfigurasi), namun pengujian alur bisnis *Acceptance* lengkap (Mulai pembuatan draf, pemilihan wilayah, upload semua lampiran, verifikasi sub-operator, lalu laporan disetujui) secara programatis belum tereksekusi seluruhnya.

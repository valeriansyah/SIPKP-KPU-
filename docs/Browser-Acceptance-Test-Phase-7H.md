# Browser Acceptance Test (Phase 7H)

Dokumen ini berisi panduan pengujian manual melalui peramban (Browser Acceptance Testing) untuk memverifikasi autentikasi, manajemen profil, akses peran (RBAC), dan isolasi data distrik.

## 1. Actor Feature Access & Isolation Matrix

Sistem ini memiliki 3 jenis aktor resmi. Verifikasikan fitur-fitur di bawah ini dengan melakukan login secara berurutan.

| Actor | Login Method | Dashboard | Profile | Create Report | Reports | District Isolation | Status |
|---|---|---|---|---|---|---|---|
| **Pelapor** | Google OAuth (`/auth/google/redirect`) | Dashboard Saya | Ya (Bisa Update HP & Nama) | Ya | Hanya Laporan Miliknya | N/A | **IMPLEMENTED** |
| **Sub Operator** | Form Login Internal (`/login`) | Dashboard Wilayah | N/A | N/A | Laporan Antrean Wilayah | **ISOLATED** (Sesuai `district_id`) | **IMPLEMENTED** |
| **Operator Provinsi**| Form Login Internal (`/login`) | Dashboard Global | N/A | N/A | Seluruh Wilayah Provinsi | **GLOBAL** (17 Kabupaten/Kota) | **IMPLEMENTED** |

---

## 2. Skenario Pengujian Pelapor (Google OAuth)

1. Buka halaman `http://127.0.0.1:8000/login` di browser Anda.
2. Klik tombol **Daftar / Masuk sebagai Pelapor** (Google Login).
3. Anda akan diarahkan ke Google. Silakan pilih akun Google Anda.
4. Setelah diizinkan, Anda akan dikembalikan ke `Dashboard Pelapor`.
5. Buka navigasi **Profil Saya** (di *sidebar*).
6. **Uji Validasi:** Kosongkan nomor HP atau ubah nama Anda, lalu klik Simpan. Nomor HP wajib diisi karena disetel otomatis sebagai `'-'` saat pendaftaran OAuth demi menjaga keutuhan basis data.
7. Buka **Buat Laporan** dan buatlah satu laporan percobaan.
8. Buka **Laporan Saya**, pastikan laporan Anda terlihat.
9. **Log out** dari sistem.

---

## 3. Skenario Pengujian Sub-Operator (District Isolation)

Pengujian ini memastikan tidak ada kebocoran laporan antarwilayah.

### 3.1. Sub-Operator Palembang
1. Buka halaman `http://127.0.0.1:8000/login`.
2. Masukkan kredensial:
   - **Email**: `subop.plg@sipkp.local` (atau `subop.palembang@sipkp.local` tergantung Seeder)
   - **Password**: `password`
3. Masuk ke **Antrean Verifikasi**. Pastikan Anda hanya melihat laporan dengan rujukan wilayah Palembang.
4. **Log out**.

### 3.2. Sub-Operator Lahat
1. Login dengan kredensial Lahat:
   - **Email**: `subop.lahat@sipkp.local`
   - **Password**: `password`
2. Masuk ke **Antrean Verifikasi**. Pastikan laporan Palembang (atau yang dibuat Pelapor di wilayah lain) **TIDAK** muncul di daftar ini.
3. **Log out**.

*(Opsional: Lakukan pada OKU, Prabumulih, atau wilayah lain sesuai kebutuhan)*

---

## 4. Skenario Pengujian Operator Provinsi (Global Access)

1. Buka halaman `http://127.0.0.1:8000/login`.
2. Masukkan kredensial:
   - **Email**: `operator@sipkp.local`
   - **Password**: `password`
3. Masuk ke navigasi **Monitoring Laporan**.
4. Anda harus bisa melihat **semua laporan** dari seluruh 17 Kabupaten/Kota, termasuk laporan yang dibuat melalui Google OAuth pada skenario Pelapor di atas.

---

## 5. Security & Constraints Validation
- **Preview Routes**: Kunjungi `http://127.0.0.1:8000/preview/pelapor`. Anda akan mendapatkan **404 Not Found** karena jalur *mocking* lokal telah dimatikan demi keamanan sesi nyata.
- **Role Bypass**: Coba masuk menggunakan surel Sub-Operator melalui tombol Google OAuth (jika ada Sub-Operator yang kebetulan surelnya terhubung ke Google). Sistem akan melempar Anda kembali ke form login dengan pesan kesalahan bahwa akun tersebut adalah akun internal.

---
**END OF TEST SCRIPT**

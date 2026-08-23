# PHASE 7G — DATABASE SYNCHRONIZATION

## 1. Kondisi Sebelum Sinkronisasi
_Database development_ lokal hanya memuat 8 Aktor:
- 1 Operator Provinsi
- 2 Sub-Operator (Palembang dan Prabumulih)
- 5 Pelapor

Sedangkan, perancangan skrip simulasi pada Phase 7G tahap 1 sesungguhnya menuntut kehadiran Sub-Operator di 17 Kabupaten/Kota. Database belum tersinkronisasi sebab larangan ketat penggunaan perintah `migrate:fresh`.

## 2. Command yang Digunakan
```bash
php artisan db:seed --class=ActorSeeder
```

## 3. Perubahan yang Dilakukan
- Skrip `ActorSeeder.php` dialihbahasakan menggunakan konstruktor Eloquent `firstOrCreate()`. Metode pengamanan _Idempotent_ ini memastikan surel Aktor yang sudah hidup di basis data tidak ditindih (dioverlaps) dan mencegah duplikasi berujung fatal (`Integrity constraint violation`).
- Nomor ponsel (`phone_number`) fiktif disematkan karena konstrain tabel `users` mewajibkan kolom *NOT NULL*.
- 15 baris Aktor Sub-Operator anyar disemai ke dalam basis data dan dikaitkan otomatis ke ID Distrik masing-masing.

## 4. Daftar 17 Akun Sub-Operator (Aktual di Database Lokal)
| Kabupaten/Kota | Email Sub Operator | district_id | Role | Ada di DB? |
|---|---|---|---|---|
| Palembang | `subop.plg@sipkp.local` | 14 | Sub Operator | **YA** |
| Prabumulih | `subop.pbm@sipkp.local` | 17 | Sub Operator | **YA** |
| Ogan Komering Ulu | `subop.ogan_komering_ulu@sipkp.local` | 1 | Sub Operator | **YA** |
| Ogan Komering Ilir | `subop.ogan_komering_ilir@sipkp.local` | 2 | Sub Operator | **YA** |
| Muara Enim | `subop.muara_enim@sipkp.local` | 3 | Sub Operator | **YA** |
| Lahat | `subop.lahat@sipkp.local` | 4 | Sub Operator | **YA** |
| Musi Rawas | `subop.musi_rawas@sipkp.local` | 5 | Sub Operator | **YA** |
| Musi Banyuasin | `subop.musi_banyuasin@sipkp.local` | 6 | Sub Operator | **YA** |
| Banyuasin | `subop.banyuasin@sipkp.local` | 7 | Sub Operator | **YA** |
| Ogan Komering Ulu Timur | `subop.ogan_komering_ulu_timur@sipkp.local` | 8 | Sub Operator | **YA** |
| Ogan Komering Ulu Selatan | `subop.ogan_komering_ulu_selatan@sipkp.local` | 9 | Sub Operator | **YA** |
| Ogan Ilir | `subop.ogan_ilir@sipkp.local` | 10 | Sub Operator | **YA** |
| Empat Lawang | `subop.empat_lawang@sipkp.local` | 11 | Sub Operator | **YA** |
| Penukal Abab Lematang Ilir | `subop.penukal_abab_lematang_ilir@sipkp.local` | 12 | Sub Operator | **YA** |
| Musi Rawas Utara | `subop.musi_rawas_utara@sipkp.local` | 13 | Sub Operator | **YA** |
| Pagar Alam | `subop.pagar_alam@sipkp.local` | 15 | Sub Operator | **YA** |
| Lubuklinggau | `subop.lubuklinggau@sipkp.local` | 16 | Sub Operator | **YA** |

## 5. Hasil Verifikasi Database (Tinker)
Skrip ekstraksi Laravel Tinker mendemonstrasikan populasi tabel `users` sukses mencapai 23 Aktor (1 Operator, 5 Pelapor, 17 Sub-Operator). Semuanya memiliki peran yang valid tanpa adanya duplikasi properti sandi/kredensial di luar skema rancangan. 

## 6. Hasil PHPUnit Regression Test
- Tests: 156
- Assertions: 413
- Passed: 156
- Failed: 0
- Errors: 0

## 7. Potensi Masalah
Tidak ada. Konstrain `NOT NULL` pada *phone_number* di struktur Model sudah diatasi secara paripurna di sisi konfigurasi penyemaian (*seeder*). Semua basis data siap menjadi zona eksploitasi dan uji peramban bagi pengguna akhir (_User Acceptance_).

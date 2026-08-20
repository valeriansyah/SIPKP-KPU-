# TESTING ACCOUNTS

Dokumen ini berisi daftar akun (*dummy credentials*) yang dapat digunakan untuk proses verifikasi *End-to-End* dan *Browser Testing* di _environment_ lokal. Kredensial ini diproduksi oleh `ActorSeeder` dan didesain secara khusus untuk mendemonstrasikan batasan-batasan (*Role-Based Access Control* / RBAC) yang terisolasi secara ketat berdasarkan status jabatan dan distrik masing-masing.

Semua pengguna di bawah ini memiliki sandi standar yang sama:
**`password`**

## 1. Operator Provinsi
Aktor dengan otorisasi tunggal tertinggi untuk pengawasan (*Monitoring*) secara global ke seluruh wilayah.
| Role | Email | Password | District | Purpose |
|---|---|---|---|---|
| Operator Provinsi | `operator@sipkp.local` | `password` | Provinsi (Global) | Testing global monitoring & administrative constraints |

## 2. Pelapor
Klien sistem yang bertugas menciptakan (*Create*) Laporan. Diisolasi absolut hanya pada hasil karyanya sendiri.
| Role | Email | Password | District | Purpose |
|---|---|---|---|---|
| Pelapor | `pelapor1@sipkp.local` | `password` | - | Testing report creation & personal isolation |
| Pelapor | *(Auto-generated)* | `password` | - | - |

## 3. Sub-Operator (17 Kabupaten/Kota)
Aktor regional dengan otorisasi eksklusif yang hanya sanggup mengakses laporan kematian di wilayah (*District*) yang menjadi tanggung jawabnya.
| Role | Email | Password | District | Purpose |
|---|---|---|---|---|
| Sub Operator | `subop.ogan_komering_ulu@sipkp.local` | `password` | Ogan Komering Ulu | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.ogan_komering_ilir@sipkp.local` | `password` | Ogan Komering Ilir | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.muara_enim@sipkp.local` | `password` | Muara Enim | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.lahat@sipkp.local` | `password` | Lahat | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.musi_rawas@sipkp.local` | `password` | Musi Rawas | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.musi_banyuasin@sipkp.local` | `password` | Musi Banyuasin | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.banyuasin@sipkp.local` | `password` | Banyuasin | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.ogan_komering_ulu_timur@sipkp.local` | `password` | Ogan Komering Ulu Timur | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.ogan_komering_ulu_selatan@sipkp.local` | `password` | Ogan Komering Ulu Selatan | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.ogan_ilir@sipkp.local` | `password` | Ogan Ilir | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.empat_lawang@sipkp.local` | `password` | Empat Lawang | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.penukal_abab_lematang_ilir@sipkp.local` | `password` | Penukal Abab Lematang Ilir | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.musi_rawas_utara@sipkp.local` | `password` | Musi Rawas Utara | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.plg@sipkp.local` | `password` | Palembang | Territorial RBAC & Backward Compatibility Test |
| Sub Operator | `subop.pagar_alam@sipkp.local` | `password` | Pagar Alam | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.lubuklinggau@sipkp.local` | `password` | Lubuklinggau | Territorial RBAC & Data Isolation |
| Sub Operator | `subop.pbm@sipkp.local` | `password` | Prabumulih | Territorial RBAC & Backward Compatibility Test |

> [!CAUTION]
> Akun dan sandi ini **HANYA** diizinkan untuk kebutuhan uji-coba lokal (_Testing Environment_). Saat aplikasi dipindahkan ke tahap produksi (_Production_), seluruh _Seeder_ Aktor akan dihilangkan dan diganti dengan kredensial otentik dari KPU.

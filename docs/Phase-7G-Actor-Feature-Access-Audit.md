# PHASE 7G — ACTOR FEATURE ACCESS AUDIT

## 1. Fitur yang benar-benar sudah Implemented
- Authentication: Login & Logout (Actor: Pelapor, Sub-Operator, Operator)
- Pelapor: Dashboard, Laporan Saya (Index), Buat Laporan (Create/Store), Detail Laporan (Show)
- Sub-Operator: Dashboard, Antrean Wilayah (Index), Detail Laporan (Show)
- Operator: Dashboard

## 2. Fitur yang baru berupa UI (Placeholder)
- Operator: Monitoring Laporan (Index) - Memiliki view dan route, tetapi konten masih berupa tulisan placeholder untuk tahap pengembangan Phase 6I.

## 3. Fitur yang belum Implemented (Not Implemented)
- Sub-Operator: Proses Verifikasi (Terima/Tolak/Revisi) Laporan (Phase 8A).
- Operator: Detail Laporan Seluruh Wilayah (Show).
- Operator: Master Data / Administrasi Sub-Operator / Aktor.

## 4. Route yang tersedia (Working)
- `login`, `logout`
- `pelapor.dashboard`, `pelapor.laporan.create`, `pelapor.laporan.store`, `pelapor.laporan.index`, `pelapor.laporan.show`, `pelapor.laporan.edit`
- `sub_operator.dashboard`, `sub_operator.antrean`, `sub_operator.laporan.show`
- `operator.dashboard`, `operator.monitoring`

## 5. Route yang missing
- `operator.laporan.show` (Belum terdefinisi)
- Master Data (Belum ada arsitekturnya sama sekali)

## 6. Route yang broken
- Tidak ditemukan rute yang rusak pasca pembersihan Phase 7F.

## 7. Tombol / Link yang masih `#`
- Menu Master Data pada bilah sisi (_Sidebar_) di Operator Provinsi.

## 8. Fitur yang menghasilkan 403 karena tidak berhak (Benar)
- Pelapor mengakses Antrean Sub-Operator atau Dashboard Sub-Operator/Operator.
- Sub-Operator mengakses Dasbor Laporan Pelapor.
- Pelapor/Sub-Operator mengakses Dasbor Operator Provinsi.
- Modifikasi _route parameter_ (contoh: Sub-Operator mengakses laporan yang bukan bagian dari distriknya) berujung `403/404` karena isolasi basis data pada _Query Builder / Service_.

## 9. Fitur yang menghasilkan 404 karena belum ada route
- Percobaan melihat Detail Laporan oleh Operator. (Belum dibuat route-nya).

## 10. Fitur yang menghasilkan 500 / Error
- Tidak ada yang terdeteksi sejauh ini, 155 test kasus tervalidasi 100% lulus.

## 11. Status RBAC Masing-Masing Aktor
- **Pelapor:** Akses dibatasi pada kepemilikan individu (`user_id`). Lulus uji isolasi.
- **Sub-Operator:** Akses dibatasi pada wilayah (`district_id`). Saat ini baru tersedia data *dummy* untuk distrik Palembang dan Prabumulih saja. Ini harus disempurnakan.
- **Operator:** Mendapatkan pandangan (_view_) bebas halangan ke seluruh wilayah untuk monitoring (saat ini *Route Controller* belum terangkai dengan Query yang nyata).

## 12. Gap yang Harus Dikerjakan di Phase 7G
1. Modifikasi `ActorSeeder` agar mencetak *dummy Sub-Operator* genap untuk 17 Kabupaten/Kota yang ada (bukan sekadar 2).
2. Perancangan skrip pengujian _Automated Tests_ (End-to-End) komprehensif (`tests/Feature/*`) guna mendemonstrasikan secara formal batas-batas wewenang dari 17 distrik tersebut (Multi-District Isolation Tests).
3. Pendokumentasian menyeluruh pada kredensial distrik (`Testing-Accounts.md`) dan matriks kesiapan aplikasi (`Project-Feature-Readiness-Matrix.md`).

# PHASE 7F — ACTOR × FEATURE × ROUTE MATRIX

## 1. Audit Result

Setelah memeriksa `routes/web.php`, `resources/views/components/layout/sidebar.blade.php`, dan `resources/views/**/*.blade.php`, serta melakukan pencocokan dengan Middleware `RoleMiddleware`, diperoleh rincian fitur berikut:

### Actor: Pelapor
| Feature | UI Entry Point | Route Name | HTTP | Controller | Middleware | Authorization | Expected | Status |
|---|---|---|---|---|---|---|---|---|
| Dashboard | Sidebar / Login | `pelapor.dashboard` | GET | `DashboardController@pelapor` | `auth, role:pelapor` | - | ALLOWED | IMPLEMENTED |
| Buat Laporan | Sidebar | `pelapor.laporan.create` | GET | `ReportController@create` | `auth, role:pelapor` | `Gate: create-report` | ALLOWED | LINK BROKEN (Href `#`) |
| Buat Laporan | Dashboard CTA | `pelapor.laporan.create` | GET | `ReportController@create` | `auth, role:pelapor` | `Gate: create-report` | ALLOWED | IMPLEMENTED |
| Laporan Saya | Sidebar | `pelapor.laporan.index` | GET | `ReportController@index` | `auth, role:pelapor` | - | ALLOWED | LINK BROKEN (Href `#`) |
| Laporan Saya | Dashboard "Lihat Semua" | `pelapor.laporan.index` | GET | `ReportController@index` | `auth, role:pelapor` | - | ALLOWED | LINK BROKEN (Href `#`) |
| Simpan Laporan | Form Submit | `pelapor.laporan.store` | POST | `ReportController@store` | `auth, role:pelapor` | `Gate: create-report` | ALLOWED | IMPLEMENTED |
| Detail Laporan | Table Action | `pelapor.laporan.show` | GET | `ReportController@show` | `auth, role:pelapor` | `ReportPolicy@view` | ALLOWED | IMPLEMENTED |
| Edit Laporan | Action (Optional) | `pelapor.laporan.edit` | GET | `ReportController@edit` | `auth, role:pelapor` | `ReportPolicy@update` | ALLOWED | IMPLEMENTED |
| Logout | Sidebar Footer | `logout` | POST | `AuthController@logout` | `auth` | - | ALLOWED | IMPLEMENTED |

### Actor: Sub-Operator
| Feature | UI Entry Point | Route Name | HTTP | Controller | Middleware | Authorization | Expected | Status |
|---|---|---|---|---|---|---|---|---|
| Dashboard | Sidebar / Login | `sub_operator.dashboard` | GET | `DashboardController@subOperator` | `auth, role:sub_operator` | - | ALLOWED | IMPLEMENTED |
| Antrean Verifikasi | Sidebar | `sub_operator.antrean` | GET | `ReportController@index` | `auth, role:sub_operator` | - | ALLOWED | LINK BROKEN (Href `#`) |
| Detail Laporan | Table Action | `sub_operator.laporan.show` | GET | `ReportController@show` | `auth, role:sub_operator` | `ReportPolicy@verify` | ALLOWED | IMPLEMENTED |
| Proses Verifikasi | Form Submit | `sub_operator.laporan.verifikasi` | POST | `VerificationController@store` | `auth, role:sub_operator` | `ReportPolicy@verify` | ALLOWED | FEATURE NOT IMPLEMENTED (Phase 8A) |
| Logout | Sidebar Footer | `logout` | POST | `AuthController@logout` | `auth` | - | ALLOWED | IMPLEMENTED |

### Actor: Operator Provinsi
| Feature | UI Entry Point | Route Name | HTTP | Controller | Middleware | Authorization | Expected | Status |
|---|---|---|---|---|---|---|---|---|
| Dashboard | Sidebar / Login | `operator.dashboard` | GET | `DashboardController@operator` | `auth, role:operator_provinsi` | - | ALLOWED | IMPLEMENTED |
| Monitoring Laporan | Sidebar | `operator.monitoring` | GET | `ReportController@index` | `auth, role:operator_provinsi` | - | ALLOWED | LINK BROKEN (Href `#`) |
| Detail Laporan | Table Action | `operator.laporan.show` | GET | `ReportController@show` | `auth, role:operator_provinsi` | `ReportPolicy@viewAny` | ALLOWED | FEATURE NOT IMPLEMENTED |
| Master Data | Sidebar | N/A | N/A | N/A | N/A | N/A | ALLOWED | FEATURE NOT IMPLEMENTED |
| Semua Aktivitas | Dashboard "Lihat Semua" | `operator.monitoring` | GET | `ReportController@index` | `auth, role:operator_provinsi`| - | ALLOWED | LINK BROKEN (Href `#`) |
| Logout | Sidebar Footer | `logout` | POST | `AuthController@logout` | `auth` | - | ALLOWED | IMPLEMENTED |

---

## 2. Problems Found

1. **Sidebar Links Broken**: Di dalam berkas `resources/views/components/layout/sidebar.blade.php`, menu navigasi sekunder (*Buat Laporan, Laporan Saya, Antrean Verifikasi, Monitoring Laporan*) tertulis sebagai `<a href="#">` dengan state `cursor-not-allowed`.
2. **Dashboard CTA Broken**: Di dalam berkas `resources/views/pelapor/dashboard.blade.php` dan `operator/dashboard.blade.php`, link "Lihat Semua" masih menggunakan `#`.
3. **Feature Not Implemented**:
   - Master Data (Operator)
   - Proses Verifikasi (Sub-Operator) - Fitur utamanya (Phase 8A) belum dibangun.
   - Halaman Monitoring Laporan (Operator) - View-nya ada (`monitoring.blade.php`) namun hanya berisi UI Placeholder text `(Phase 6I)`.

## 3. Root Causes
Pada masa pengembangan rancangan UI purwarupa (Phase 5C), navigasi sekunder sengaja dibekukan (*disabled*) agar klien hanya berfokus pada _Review_ halaman _Dashboard Utama_. Rute aktual telah selesai digarap di Phase 7A-7D, namun modifikasi pengkaitan rute di bilah sisi (*sidebar*) dan Dasbor belum diselaraskan kembali dengan fitur yang sudah hidup.

## 4. Next Step
Tahapan perbaikan akan diformulasikan ke dalam Dokumen Implementasi (`implementation_plan.md`) dan menunggu perizinan dari otoritas (USER) sebelum kode dimutakhirkan.

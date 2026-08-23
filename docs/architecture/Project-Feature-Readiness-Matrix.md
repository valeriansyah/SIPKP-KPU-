# Project Feature Readiness Matrix

Matriks kesiapan fitur (Feature Readiness Matrix) yang merepresentasikan kapabilitas setiap aktor yang benar-benar **DAPAT DIGUNAKAN (IMPLEMENTED)** dalam aliran aplikasi sesungguhnya, tanpa rute Preview.

## Pelapor (Masyarakat Umum)

| Fitur | Status | Deskripsi |
| :--- | :--- | :--- |
| **Google OAuth** | IMPLEMENTED | Pendaftaran/Login via Socialite `auth/google/redirect` |
| **Login** | IMPLEMENTED | Otorisasi peran Pelapor via Google OAuth |
| **Profile** | IMPLEMENTED | Dapat memperbarui nama & No. HP (Update Constraint) |
| **Create Report** | IMPLEMENTED | Pengajuan Laporan Kematian dengan Dokumen |
| **My Reports** | IMPLEMENTED | Melihat senarai laporan pengajuannya sendiri |
| **Report Detail** | IMPLEMENTED | Melihat rincian & status verifikasi laporan |
| **Logout** | IMPLEMENTED | Penghancuran sesi (*Session invalidation*) |

## Sub-Operator (Admin Wilayah)

| Fitur | Status | Deskripsi |
| :--- | :--- | :--- |
| **Login** | IMPLEMENTED | Kredensial email/pass internal. Dilarang via Google |
| **Dashboard** | IMPLEMENTED | Statistik spesifik wilayah tugasnya |
| **Queue/Reports** | IMPLEMENTED | Melihat antrean laporan yang masuk ke wilayahnya |
| **Detail** | IMPLEMENTED | Mengecek detail berkas sebelum divalidasi |
| **District Isolation**| IMPLEMENTED | RBAC Ketat: Laporan 17 Distrik tidak saling silang |
| **Logout** | IMPLEMENTED | Penghancuran sesi |

## Operator Provinsi (Admin Global)

| Fitur | Status | Deskripsi |
| :--- | :--- | :--- |
| **Login** | IMPLEMENTED | Kredensial akun `operator@sipkp.local` |
| **Dashboard** | IMPLEMENTED | Statistik himpunan dari seluruh wilayah |
| **Monitoring** | IMPLEMENTED | Melihat laporan yang diajukan di wilayah mana pun |
| **Global Access** | IMPLEMENTED | Memiliki otoritas pemantauan 17 Kabupaten/Kota |
| **Logout** | IMPLEMENTED | Penghancuran sesi |

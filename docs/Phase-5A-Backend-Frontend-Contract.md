# Phase 5A: Backend–Frontend Contract & UI Architecture Blueprint

## 1. Scope
Dokumen ini merupakan blueprint hasil audit backend untuk mempersiapkan rancangan antarmuka pengguna (Frontend) bagi SIPKP KPU Provinsi Sumatera Selatan. Dokumen ini memastikan frontend akan berinteraksi sesuai batasan validasi dan otorisasi yang telah dibentuk secara ketat pada sisi Backend.

## 2. Backend Inventory
Seluruh komponen utama backend yang terkait (Authentication, Authorization, Service Layer: Report, Document, Verification) telah diperiksa. Database schema konsisten.

## 3. Route Inventory
- **Authentication**: `GET /login`, `POST /login`, `GET /register`, `POST /register/send-otp`, `POST /register/verify-otp`, `POST /register`, `POST /logout`, `POST /forgot-password/...`
- **Reports**: Resource API (Web routes) di `/reports` yang menghandle `index`, `store`, `show`, `update`.
- **Documents**: `POST /reports/{report}/documents`, `POST /documents/{document}`, `DELETE /documents/{document}`.
- **Verification**: `POST /reports/{report}/verify`.

## 4. Authentication Contract
- Frontend menggunakan Web Session Authentication.
- Setelah Auth berhasil, backend men-set cookie Session ID (XSRF/Cookie based).
- Logout menggunakan `POST /logout` dengan CSRF Token.

## 5. Authorization Contract
Frontend mematuhi aturan Role dan Scope berbasis backend.
- **Operator**: Akses global (`/reports` mengembalikan semua), dilarang melakukan aksi verify.
- **Sub Operator**: Scope data lokal (district), diizinkan melakukan verifikasi data laporan lokal.
- **Pelapor**: Hanya data miliknya (User ID constraint), akses read/update terbatas pada laporan dengan status mengizinkan (Pending/Perlu Perbaikan).

## 6. Report Contract
- GET `/reports`: List laporan. Pelapor melihat datanya, Sub-operator melihat data district-nya, Operator melihat seluruhnya.
- POST `/reports`: Membuat laporan baru, khusus Role Pelapor. District_id didapatkan dari `deceased.district_id`, bukan user session (Pelapor bisa melapor kasus luar kotanya, walau logic default district mengikuti session jika tidak spesifik).
- PUT/PATCH `/reports/{report}`: Hanya bisa diupdate Pelapor saat status "Perlu Perbaikan".
- Semua dependensi internal identity (`user_id`, `district_id` dari pelapor) bersifat **SERVER DERIVED**.

## 7. Document Contract
- POST `/reports/{report}/documents`: Memerlukan `document_type_id` (dari master data) dan file (maksimum 5 MB, `pdf,jpg,jpeg,png`).
- POST `/documents/{document}`: Untuk melakukan penggantian (Replace). Backend menerapkan Soft Delete.
- DELETE `/documents/{document}`: Menghapus dokumen (Soft Delete).
- Hanya diperbolehkan ketika status laporan: "Pending" atau "Perlu Perbaikan".

## 8. Verification Contract
- POST `/reports/{report}/verify`
- Allowed Values for `decision`: `diproses`, `perlu_perbaikan`, `disetujui`, `ditolak`.
- Backend memvalidasi transisi (status final tidak bisa ditarik).
- Identity *verifier* 100% **SERVER DERIVED**. 

## 9. Request Validation Contract
- Format validasi dikembalikan dengan HTTP status **422 Unprocessable Entity** (jika API) atau HTTP **302 Redirect** back dengan Session Errors (jika Web Endpoint Form). 
- Frontend WAJIB menangani format error dari `errors` bag.

## 10. Response Contract
- **SUCCESS**: HTTP 200/201, dengan payload JSON atau view render.
- **VALIDATION ERROR**: 302 Redirect with errors (Web) atau 422 (JSON).
- **UNAUTHORIZED**: 302 Redirect to `/login` (Web) atau 401 (JSON).
- **FORBIDDEN**: HTTP 403.
- **NOT FOUND**: HTTP 404.

## 11. Error Contract
- Validasi gagal: Menampilkan pesan per-field (Contoh: "Kolom nama tidak boleh kosong").
- Transition error (contoh verifikasi pada final state): Mengembalikan JSON Exception message dengan kode error (422) pada Service endpoint.

## 12. Status Mapping
| Status | Siapa yang melihat | Siapa yang mengubah | Siapa yang Verifikasi | Label/Button (Next State) |
|--------|--------------------|---------------------|-----------------------|---------------------------|
| **Pending** | Semua Role terkait | Pelapor (Dokumen) | Sub Operator | Action: **Proses** -> `Diproses` |
| **Diproses** | Semua Role terkait | - | Sub Operator | Action: **Approve/Reject/Revisi** |
| **Perlu Perbaikan**| Semua Role terkait | Pelapor (Form/Dokumen)| - | Action: (By Pelapor) Submit Ulang |
| **Disetujui** | Semua Role terkait | - | - | (FINAL) - No Action |
| **Ditolak** | Semua Role terkait | - | - | (FINAL) - No Action |

## 13. Role-based UI Architecture
- **Pelapor Dashboard**: Fokus pada "Laporan Saya", "Buat Laporan Baru", "Perbaiki Laporan". 
- **Sub Operator Dashboard**: Fokus pada "Antrean Verifikasi (District)", "Riwayat Verifikasi (District)".
- **Operator Dashboard**: Statistik Global, Master Data (District, Status), Log Audit, Manajemen Akun Sub-Operator.

## 14. Navigation Architecture
Sistem menggunakan Layout Role-Based, Navigation Menu disembunyikan menggunakan tag kontrol otorisasi atau conditional rendering (jika SPA) berdasarkan property dari auth user (`role_id` / `role_name`).

## 15. Page Inventory
- Public: `/`, `/login`, `/register`, `/forgot-password`
- Dashboard: `/operator/dashboard`, `/sub-operator/dashboard`, `/pelapor/dashboard`
- Resource: `/reports`, `/reports/{id}`
- Setup Data: `/operator/master-data/...` (TBD Phase berikutnya)

## 16. Data Requirements
Data yang mengalir ke view harus dipaginate untuk `index`.
Object Report selalu melibatkan eager loading: `deceased`, `reportStatus`, `documents.documentType`, `reportVerifications`.

## 17. Missing Backend Functionality
- **Dashboard Metrics Endpoint**: (Belum dibuat). Chart/Statistik agregasi per role.
- **Master Data CRUD Endpoint**: (Belum dibuat untuk Operator mengatur User/Master).
*(Tandai sebagai: BACKEND MISSING, dilanjutkan ke Phase selanjutnya)*

## 18. Business Decisions
- Tidak ada kontradiksi ekstrem yang membutuhkan klarifikasi ulang (semua rules selaras dan berhasil dites).

## 19. Security Considerations
- Frontend TIDAK BOLEH mem-bypass status validation. 
- Button/Form submission wajib menyertakan valid CSRF token.
- Parameter URL (seperti `report_id`) dijamin terlindungi oleh Policy, tidak perlu enkripsi tambahan untuk ID (meskipun UUID lebih disarankan ke depan, namun ID auto-increment sudah aman lewat Policy).

## 20. Phase 5B Recommendations
- Gunakan arsitektur view Blade dengan Alpine.js untuk interaktivitas komponen modal (atau framework modern jika diputuskan KPU).
- Siapkan komponen Reusable: `<x-status-badge>`, `<x-document-viewer>`, `<x-audit-timeline>`.
- Prioritaskan UX flow pelapor pada pembuatan entitas.

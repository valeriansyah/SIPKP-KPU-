# Phase 5B: UI/UX Design Specification & Component Architecture

## 1. Design System

### 1.1 Brand Direction
Sistem SIPKP KPU Provinsi Sumatera Selatan harus memancarkan kesan **Professional, Government / Institutional, Trustworthy, Clean, dan Accessible**. Karena ini adalah aplikasi instansi pemerintahan yang mengolah data sensitif (kematian penduduk pemilih), desain harus sangat fungsional, tidak terlalu dekoratif, dan fokus pada kejelasan alur kerja (workflow clarity).

### 1.2 Color System
Menggunakan semantic naming:
- `color.primary`: KPU Brand Color (misalnya #FF8C00 / Orange KPU atau warna institusional standar jika ada, jika tidak fallback ke Blue #1E3A8A). *Design Decision Required: Warna resmi KPU Sumsel*.
- `color.primary-hover`: Nuansa lebih gelap dari primary untuk efek hover.
- `color.secondary`: Gray-600 (#4B5563) untuk elemen sekunder.
- `color.background`: Slate-50 (#F8FAFC) untuk latar belakang utama.
- `color.surface`: White (#FFFFFF) untuk Cards, Modals, Forms.
- `color.border`: Gray-200 (#E5E7EB) untuk garis batas yang clean.
- `color.text-primary`: Slate-900 (#0F172A) untuk teks utama.
- `color.text-secondary`: Slate-500 (#64748B) untuk helper text/label.
- `color.success`: Emerald-600 (#059669) - Disetujui/Sukses.
- `color.warning`: Amber-500 (#F59E0B) - Pending/Perlu Perbaikan.
- `color.danger`: Red-600 (#DC2626) - Ditolak/Error.
- `color.info`: Blue-500 (#3B82F6) - Diproses/Informasi.

### 1.3 Typography
- **Font Family**: Inter atau Roboto (Bawaan sistem web modern, highly readable).
- **Heading Hierarchy**: H1 (Page Title: 24px/bold), H2 (Section Title: 20px/semibold), H3 (Card Title: 16px/medium).
- **Body**: 14px reguler untuk data tabel/paragraf.
- **Caption/Helper**: 12px untuk petunjuk input form.
- **Label**: 12px/semibold, uppercase untuk form label.
- **Button text**: 14px/medium.

### 1.4 Spacing
Mengacu pada scale 4px (Tailwind standard): `space-1 (4px), space-2 (8px), space-4 (16px), space-6 (24px), space-8 (32px)`.

### 1.5 Border Radius
- Card/Modal: `rounded-xl` (12px)
- Button/Input: `rounded-md` (6px)
- Badge: `rounded-full` (9999px)

### 1.6 Shadow
- Elevasi standar (`shadow-sm`) untuk card.
- `shadow-lg` untuk modal dan dropdown agar menonjol dari surface.

## 2. Layout Architecture
### Global Shell
```text
Application Shell
├── Sidebar / Navigation (Kiri, statis di Desktop, collapsed di Mobile)
├── Topbar (Berisi informasi User, Role, District, Breadcrumb, Logout Button)
├── Main Content (Background: Slate-50)
└── Notification / Toast Area (Pojok kanan atas)
```
**Responsive Behavior:** 
- Desktop: Sidebar permanen.
- Tablet (md): Sidebar dapat diciutkan (mini-sidebar).
- Mobile (sm): Sidebar menjadi drawer overlay yang muncul via Hamburger Menu. Topbar diringkas.

## 3. Role-Based Navigation
- **Operator** (Monitoring/Admin Global):
  - Dashboard
  - Laporan Global
  - Master Data
  - Manajemen Sub Operator
  - Audit Log
  - Profil
  *(Action Verifikasi sepenuhnya disembunyikan)*

- **Sub Operator** (District Verification):
  - Dashboard (Data District)
  - Antrean Laporan (Filter: District otomatis)
  - Profil

- **Pelapor** (Self-Service Data Entry):
  - Dashboard
  - Laporan Saya
  - Buat Laporan Baru
  - Profil

## 4. Page Inventory
| Page | Role | Purpose | Route | Main Components | Primary Action |
| ---- | ---- | ------- | ----- | --------------- | -------------- |
| Landing Page | Public | Pengenalan SIPKP | `/` | Hero, Info Cards | Login / Register |
| Login | Public | Auth Masuk | `/login` | AuthForm | Submit Login |
| Register | Public | Auth Daftar | `/register` | AuthForm, OTPInput | Verifikasi OTP -> Daftar |
| Forgot/Reset | Public | Pulihkan Sandi | `/forgot-password`| AuthForm, OTPInput | Verifikasi OTP -> Ganti |
| Pelapor Dash. | Pelapor | Ringkasan Data | `/pelapor/dashboard`| StatCard, RecentTable | "Buat Laporan Baru" |
| Buat Laporan | Pelapor | Form Entry | `/reports/create` | Multi-step Form (Data + File)| Submit |
| Laporan Saya | Pelapor | Daftar Kasus | `/reports` | DataTable | View Detail |
| Report Detail | Pelapor | Track Status & Revisi| `/reports/{id}` | StatusTimeline, FileUpload | (Revisi) Submit Ulang |
| SubOp Dash. | Sub Operator | Monitoring Wilayah | `/sub-operator/dashboard`| StatCard, RecentTable | "Verifikasi Antrean" |
| Report Queue | Sub Operator | Daftar Laporan Wilayah| `/reports` | DataTable | View Detail |
| Verification Panel | Sub Operator | Pemeriksaan Berkas | `/reports/{id}` | InfoCard, DocumentViewer, FormVerifikasi | Approve/Reject/Revisi |
| Operator Dash. | Operator | Ringkasan Global | `/operator/dashboard` | StatCard, Chart | View Reports |
| Global Reports | Operator | Monitoring Seluruh Data| `/reports` | DataTable (Global) | View Detail (Read-Only) |

## 5. Dashboard Specification
### 5.1 Pelapor Dashboard
- **Metrik**: Total Laporan, Status (Pending, Diproses, Disetujui, Ditolak).
- **Recent Reports**: Tabel 5 laporan terakhir.
- **CTA**: Tombol besar "Buat Laporan Kematian Baru".

### 5.2 Sub Operator Dashboard
- **Metrik**: Jumlah Laporan Masuk di District-nya, Menunggu Verifikasi (Pending), Sedang Diproses.
- **Antrean Prioritas**: Laporan dengan status `Pending` diurutkan dari terlama.
- **Identitas**: Tampilkan nama Kabupaten/Kota di Topbar.

### 5.3 Operator Dashboard
- **Metrik**: Total Seluruh Laporan Provinsi, Agregasi status.
- **Statistik District**: Chart bar wilayah dengan laporan tertinggi.
- **Audit Summary**: Log aktivitas krusial terakhir.

## 6. Report Table Design
Tabel menggunakan pagination server-side, search/filter otomatis, serta responsif (scroll-x di mobile).
**Kolom Berdasarkan Role**:
- **Pelapor**: No, Nama Almarhum, Tgl. Lapor, Status Badge, Aksi (Lihat/Revisi).
- **Sub Operator**: No, Nama Almarhum, NIK, Tgl. Lapor, Status Badge, Aksi (Verifikasi).
- **Operator**: No, Nama Almarhum, Kabupaten/Kota, Pelapor, Status Badge, Aksi (Lihat).

## 7. Report Detail Page
```text
Report Detail
├── Header (No. Laporan, Status Badge, Tgl. Dibuat)
├── Alert Banner (Khusus jika status "Perlu Perbaikan", tampilkan catatan penolakan)
├── Deceased Information (Grid NIK, Nama, TTL, dll)
├── Reporter Information (Kontak Pelapor)
├── Documents (Grid Card untuk tiap tipe dokumen)
├── Verification History (Timeline status)
└── Action Panel (Conditionally rendered, letaknya sticky bottom atau right sidebar)
```

## 8. Status Design
| Status | Semantic | Pelapor | Sub Operator | Operator |
| ------ | -------- | ------- | ------------ | -------- |
| **Pending** | `warning` | Read + Ubah Dokumen | Tombol **Proses** | Read-only |
| **Diproses**| `info` | Read-Only | Tombol **Setujui/Tolak/Revisi**| Read-only |
| **Perlu Perbaikan**| `warning` | Ubah Data + Dokumen | Read-Only | Read-only |
| **Disetujui** | `success`| Read-Only (FINAL) | Read-Only (FINAL) | Read-only |
| **Ditolak** | `danger` | Read-Only (FINAL) | Read-Only (FINAL) | Read-only |

## 9. Verification UI (Khusus Sub Operator)
Berada di halaman Detail Laporan (`/reports/{id}`). Jika status `Pending` atau `Diproses`:
- **Current Status**: Ditampilkan jelas.
- **Decision Dropdown**: Pilih (Proses, Disetujui, Ditolak, Perlu Perbaikan).
- **Notes Textarea**: Input wajib jika Keputusan = Ditolak / Perlu Perbaikan.
- **Submit Button**: "Simpan Keputusan Verifikasi" (Memanggil `POST /reports/{id}/verify`).
- *Forbidden Check*: Jika pengguna Pelapor/Operator, block UI Verification Panel ini sama sekali (dihilangkan).

## 10. Document Upload UI
Komponen `Document Upload Card`:
- **Header**: Tipe Dokumen (mis. "KTP Almarhum").
- **Badge**: Wajib (Merah/Abu) atau Opsional (Biru).
- **State**:
  - `Missing`: Area dropzone upload, instruksi "Maks. 5MB (PDF/JPG/PNG)".
  - `Uploaded`: Nama file, icon sukses, tombol Preview/Download.
  - `Action`: Tombol **Replace** (Ganti) jika status laporan mengizinkan. Tombol **Delete** khusus dokumen opsional.
- **Loading State**: Circular spinner saat proses post AJAX.

## 11. Form Design
- Input selalu disertai elemen `<label>` yang jelas.
- Wajib memiliki asteris `*` merah untuk required.
- **Error State**: Outline input menjadi merah muda, Helper text di bawah menjadi merah berisi pesan error validasi (`422`).
- **Loading State**: Tombol submit ter-disable dan menampilkan ikon spinner.

## 12. Authentication UI
- Flow sesuai aturan kontrak backend.
- UI OTP: Input 6 digit (satu kotak per angka) dengan auto-focus ke angka selanjutnya, link "Kirim Ulang OTP" dengan countdown.

## 13. Feedback States
- **Success (200/201)**: Toast notification di pojok atas (Hijau).
- **Error/Validasi (422/302 back)**: Label merah di input terkait.
- **403 Forbidden**: Empty State illustration (Gembok), pesan aman "Anda tidak memiliki akses untuk aksi ini".
- **404 Not Found**: Illustration "Data tidak ditemukan".
- **Empty State Tabel**: Icon folder terbuka, teks "Belum ada laporan kematian yang diajukan".

## 14. Component Architecture
```text
Layout: AppShell, Sidebar, Topbar, Breadcrumb
Navigation: NavItem (Active/Inactive), UserMenu (Dropdown Logout)
Data Display: StatCard, DataTable, StatusBadge, EmptyState, Pagination
Forms: Input, Select, Textarea, OTPInput, FileUploader (Dropzone)
Feedback: Alert (Toast), Modal, LoadingSpinner
Reports: ReportDetailCard, VerificationTimeline
Documents: DocumentCard
```

## 15. Responsive Design
- Desktop: Full sidebar dan tabel lengkap.
- Tablet: Sidebar dapat di-collapse, tabel padding berkurang.
- Mobile: Sidebar beralih ke Drawer/Hamburger. Tabel beralih menjadi format *Cards* atau diberikan horizontal scroll. Action Verifikasi beralih menjadi full-screen modal/bottom sheet.

## 16. Accessibility
- Warna teks memiliki rasio kontras 4.5:1 terhadap background.
- Status Badge disertai Ikon (bukan sekadar warna) (e.g. Centang Hijau, Silang Merah).
- Fokus state jelas pada navigasi keyboard.

## 17. UI Security Principles
- UI visibility is NOT authorization.
- Panel Verifikasi disembunyikan bagi Pelapor dan Operator di UI, NAMUN backend `VerifyReportRequest` dan `ReportPolicy` tetap menjadi tameng absolut mencegah akses paksa (POST request via CURL/Postman).
- District ID Sub Operator ditentukan backend (`$request->user()->district_id`), sama sekali tidak ada hidden input HTML untuk ID District (Mencegah Tampering).

## 18. UX Flow Diagram
**Sub Operator Flow**:
`Login -> Dashboard -> Antrean Laporan -> Detail (Status: Pending) -> Klik 'Proses' -> Detail (Status: Diproses) -> Review Dokumen Lengkap -> Form Keputusan (Setuju/Tolak) -> Klik Submit -> Status Final & Toast Berhasil.`

## 19. Design Decision Register
| ID | Decision | Status | Reason |
| -- | -------- | ------ | ------ |
| 1 | Warna Utama KPU (Orange) | ASSUMPTION | Menunggu branding guide instansi |
| 2 | Status Transisi Final | CONFIRMED | `Disetujui` dan `Ditolak` permanent |
| 3 | Operator Dashboard Chart | DESIGN DECISION REQUIRED | Jenis statistik apa yang paling dibutuhkan? (Saat ini asumsi agregasi global) |

## 20. Implementation Guideline Phase 5C
1. Set-up TailwindCSS & Alpine.js/Blade Components.
2. Bangun `AppShell` (Layout, Sidebar, Navbar).
3. Buat Komponen reusable (Buttons, Inputs, StatusBadge, DataTable).
4. Implementasikan Authentication Views.
5. Implementasikan Halaman Pelapor (fokus CRUD).
6. Implementasikan Halaman Sub-Operator (fokus Panel Verifikasi).
7. Hubungkan semua Form ke API/Endpoints backend. Implementasikan error handling (422 Unprocessable Entity) secara seragam.

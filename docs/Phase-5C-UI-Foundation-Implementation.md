# Phase 5C: UI Foundation & Dashboard Prototype Implementation

## 1. Design Tokens & Color System
Menggunakan Tailwind CSS v4 `@theme` directive (tanpa tailwind.config.js) di dalam `resources/css/app.css` untuk menetapkan Custom Properties CSS secara native.

**Brand Colors (KPU):**
- Primary (Maroon): `#8B0000`
- Primary Dark: `#660000`
- Primary Light: `#B22222`
- Accent (Gold): `#D4AF37`

**Semantic Colors:**
- Background: `#F8FAFC` (Slate 50)
- Surface/Card: `#FFFFFF` (White)
- Text: `#1E293B` (Slate 800)
- Muted: `#64748B` (Slate 500)
- Success: `#10B981` (Emerald 500)
- Warning: `#F59E0B` (Amber 500)
- Danger: `#EF4444` (Red 500)
- Info: `#3B82F6` (Blue 500)

**Typography:**
- Font Family Default: `Inter`, system-ui.

## 2. Layout Architecture
- File Utama: `resources/views/layouts/app.blade.php`.
- Menggunakan CSS Grid/Flexbox modern untuk membuat struktur Application Shell (Sidebar + Topbar + Content).
- Sidebar: Fixed di sebelah kiri pada Desktop, Collapsible Drawer (via Hamburger Menu) di Mobile.
- Topbar: Berisi breadcrumb, indikator District, dan profil pengguna aktif.
- Menjaga dependensi JS seminimal mungkin (vanilla JS untuk toggle menu sidebar) agar maintainable.

## 3. Navigation Architecture
Sistem navigasi didefinisikan secara role-aware di dalam `resources/views/components/layout/sidebar.blade.php`.
- **Operator**: Memiliki akses navigasi global (Dashboard Global, Monitoring, Master Data).
- **Sub Operator**: Memiliki akses navigasi scope wilayah (Dashboard Wilayah, Antrean Verifikasi).
- **Pelapor**: Memiliki akses self-service (Dashboard Saya, Buat Laporan, Laporan Saya).
*Semua security enforcement tetap dijalankan di layer Middleware dan Policy.*

## 4. Component Architecture
Komponen reusable dipisahkan di dalam namespace `x-ui` dan `x-layout` untuk mencegah code duplication:
- `x-layout.sidebar`: Render sidebar berbasis peran.
- `x-layout.topbar`: Header utama dan Action User.
- `x-ui.card`: Box/Panel putih standar dengan slot header/footer opsional.
- `x-ui.button`: Komponen tombol standar dengan varian (primary, secondary, outline, danger) dan ukuran (sm, md, lg).
- `x-ui.badge`: Label status generik.
- `x-reports.status-badge`: Badge khusus Status Laporan (Pending, Diproses, Disetujui, Ditolak, Perlu Perbaikan) lengkap dengan Icon SVG yang sesuai untuk mematuhi prinsip aksesibilitas (Warna + Ikon).

## 5. Dashboard Prototypes
Semua dashboard saat ini merender *PROTOTYPE DATA* secara statis untuk memberikan blueprint visual. 
- **Operator Dashboard** (`resources/views/operator/dashboard.blade.php`): Menampilkan metrik agregat global (Total Laporan, Pending, Diproses, dll.), placeholder chart distribusi Kabupaten, dan log aktivitas audit.
- **Sub Operator Dashboard** (`resources/views/sub-operator/dashboard.blade.php`): Menampilkan scope district di topbar, metrik antrean, dan tabel antrean verifikasi dengan penekanan aksi "Lihat Detail" / "Lanjut Verifikasi".
- **Pelapor Dashboard** (`resources/views/pelapor/dashboard.blade.php`): Menampilkan statistik laporan pribadi, tombol call-to-action primer "Buat Laporan Baru", dan daftar laporan terbaru dengan indikator status warna-warni.

## 6. Responsive Strategy & Accessibility
- Layar kecil memicu disembunyikannya Sidebar dan dimunculkannya tombol Hamburger menu.
- Tabel laporan memiliki class `overflow-x-auto` agar horizontal scrollable di layar sempit tanpa merusak layout global.
- Semua elemen interaktif memiliki `focus:ring-2` untuk navigasi keyboard.
- Penggunaan rasio kontras warna tinggi (teks di atas background).

## 7. Mock/Prototype Data Strategy & Backend Gaps
**Backend Gaps Identified**:
1. Belum ada API/Service khusus untuk mengambil agregasi metrik statistik (Count By Status).
2. Belum ada endpoint untuk Chart Sebaran Kabupaten.
3. Belum ada sistem Audit Log yang lengkap.

**Strategi:** Semua metrik di dashboard saat ini di-*hardcode* dengan anotasi `PROTOTYPE DATA — REPLACE WITH BACKEND METRICS`. Pada Phase berikutnya (5D atau 6), sebuah `DashboardService` akan dipanggil oleh controller untuk meng-inject variable dinamis `$metrics` ke Blade Views ini.

## 8. Testing & Validation Results
- **Automated Tests**: Dibuat `UIFoundationTest.php` untuk memvalidasi render komponen berdasarkan role serta memvalidasi redireksi unauthorized.
- **Test Results**: 100% Passed.
- **Regression Results**: 100% Passed (Seluruh fungsi autentikasi, otorisasi, dan service logic Phase sebelumnya tetap berjalan).

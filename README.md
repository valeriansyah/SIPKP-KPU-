# SIPKP
Sistem Informasi Pelaporan Kematian Pemilih

## Tentang Sistem
SIPKP dirancang untuk mendukung pelaporan kematian pemilih pada KPU Provinsi Sumatera Selatan secara digital, aman, dan efisien.

## Role
- Pelapor
- Sub Operator
- Operator Provinsi

## Fitur Utama
- Authentication
- Google OAuth
- Pelaporan kematian
- Private document upload
- Verification workflow
- Targeted revision workflow
- District isolation
- Email notification
- Operator monitoring/filter
- Master data
- RBAC

## Teknologi
- Laravel ^13.8
- PHP ^8.3
- SQLite untuk development
- Blade
- Vite
- JavaScript

## Installation Development

```bash
git clone https://github.com/valeriansyah/SIPKP-KPU-.git sipkp
cd sipkp
composer install
npm install
cp .env.example .env
# Buat database SQLite (contoh: touch database/database.sqlite)
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
php artisan serve
```

## Testing
```bash
php artisan test
npm run build
```
Baseline: 191 tests, 566 assertions, 0 failed.

## Dokumentasi
Untuk panduan detail serah terima dan arsitektur sistem, silakan baca:
[HANDOVER_SIPKP.md](docs/deployment/HANDOVER_SIPKP.md)

## Catatan Production
- Project ini berada pada tahap handover.
- Deployment ke production server belum dilakukan.
- Konfigurasi environment production menjadi tanggung jawab tim internal KPU.
- **Penting**: Jangan pernah meng-commit file `.env` atau credential asli (password, client secret, SMTP) ke dalam repositori.

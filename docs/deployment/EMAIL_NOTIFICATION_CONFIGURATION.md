# Konfigurasi Email Notification SIPKP

Dokumen ini memandu administrator atau developer dalam mengonfigurasi fitur notifikasi email otomatis SIPKP (Sistem Informasi Perekapan Kasus Pemilu) untuk lingkungan Development dan Production.

## 1. Development / Demo

Pada lingkungan development atau demo, SIPKP dapat dikonfigurasi untuk menggunakan penyedia layanan email gratis seperti Gmail. 

Contoh konfigurasi `.env`:
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email-testing@gmail.com
MAIL_PASSWORD=APP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email-testing@gmail.com
MAIL_FROM_NAME="SIPKP KPU Provinsi Sumatera Selatan"
```

**Panduan Keamanan Development:**
- **Gunakan Gmail testing khusus:** Jangan pernah menggunakan akun email pribadi atau akun KPU resmi untuk testing.
- **Gunakan Google App Password:** `MAIL_PASSWORD` wajib diisi menggunakan _App Password_ (Sandi Aplikasi) dari Google, **bukan** password utama akun Gmail.
- **Jangan commit `.env`:** File `.env` bersifat rahasia dan sudah dimasukkan ke `.gitignore`. Dilarang menyebarluaskan kredensial SMTP ke _version control_ (Git).

## 2. Production KPU

Source code SIPKP dibangun secara **provider-neutral** melalui fasad Laravel Mail, sehingga sistem sama sekali tidak terikat atau bergantung pada server Gmail.

Saat di-_deploy_ ke infrastruktur KPU, administrator cukup mengubah variabel pada berkas `.env` server tanpa menyentuh source code (`.php`).

Contoh generic konfigurasi `.env` untuk server produksi:
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=<SMTP_KPU>
MAIL_PORT=<PORT>
MAIL_USERNAME=<EMAIL_RESMI_KPU>
MAIL_PASSWORD=<PASSWORD_OR_APP_PASSWORD>
MAIL_ENCRYPTION=<tls/ssl>
MAIL_FROM_ADDRESS=<EMAIL_RESMI_KPU>
MAIL_FROM_NAME="SIPKP KPU Provinsi Sumatera Selatan"
```

**Identitas Pengirim yang Disarankan:**
- `MAIL_FROM_NAME`: SIPKP KPU Provinsi Sumatera Selatan
- `MAIL_FROM_ADDRESS`: (email resmi yang disediakan oleh administrator KPU, misal: sipkp@kpu-sumsel.go.id)

## 3. Setelah Mengubah .env

Setelah Anda memodifikasi variabel `MAIL_*` pada `.env`, wajib menjalankan _command_ berikut pada terminal server untuk memperbarui *cache* konfigurasi aplikasi:
```bash
php artisan optimize:clear
php artisan config:cache
```

## 4. Security

Untuk meminimalisir risiko peretasan atau kebocoran data (_data breach_), perhatikan _best-practice_ keamanan berikut:
- **Jangan commit `.env`:** Selalu pastikan `.env` terabaikan oleh Git.
- **Jangan _hardcode_ credential:** Tidak boleh ada _username_, *port*, atau _password_ SMTP yang ditulis langsung ke dalam _controller_ atau _notification class_.
- **Gunakan Sandi Aplikasi (App Password):** Jika penyedia layanan (provider SMTP) memiliki fitur _App Password_, gunakan itu. Hindari pemakaian _password_ utama akun.
- **Batasi akses `.env` di server:** Pastikan _file permission_ pada berkas `.env` (misalnya `chmod 600`) hanya dapat dibaca oleh _user web-server_.
- **Rotasi credential jika bocor:** Segera _revoke_ atau buat ulang sandi jika terindikasi adanya kebocoran.
- **Gunakan akun email khusus aplikasi:** Jangan mencampur-adukkan akun operasional manusia dengan akun bot notifikasi SIPKP.

## 5. Verification Setelah Deployment

Segera lakukan *Post-Deployment Verification* untuk menjamin kesiapan fitur notifikasi:
1. **Test email Perlu Perbaikan:** Buat laporan percobaan, lalu minta akun Sub Operator untuk mengubah status ke "Perlu Perbaikan".
2. **Test email Disetujui:** Lanjutkan laporan percobaan tersebut, lalu ubah statusnya ke "Disetujui".
3. **Cek Inbox / Spam:** Buka kotak masuk email Pelapor penguji. Pastikan subjek, nama pengirim, konten perbaikan, dan tautan masuk sistem muncul dengan tepat dan tidak ada berkas lampiran privat yang tertaut.
4. **Cek Laravel Log:** Jika pengiriman terindikasi gagal, periksa jurnal galat sistem di `storage/logs/laravel.log`. Fitur _failure-handling_ pada aplikasi akan mencatat rincian kegagalan SMTP ke _log_ tanpa menginterupsi _user_.
5. **Pastikan status laporan tersimpan:** Validasi bahwa status laporan di layar Dasbor Pelapor/Sub Operator tetap berubah seperti biasa kendati terjadi malfungsi SMTP (koneksi lambat/putus).

## 6. Catatan

Tidak diperlukan modifikasi _source code_ untuk beralih dari satu layanan SMTP (seperti Gmail) ke layanan lain (SMTP Internal KPU). Hanya konfigurasi _environment variable_ pada `.env` yang perlu diubah.

# IMPAL-Kelompok-06
Tugas Besar Implementasi dan Pengujian Perangkat Lunak. Merancang aplikasi peminjaman buku berbasis website.

## Tech Stack

- Laravel 13
- PHP 8.5.12 (minimal PHP 8.3)
- Composer
- Node.js (untuk Vite / aset frontend)
- SQLite (default Laravel 13), bisa diganti MySQL lewat `.env`

---

## Persiapan Environment (Windows)

Laravel 13 butuh PHP 8.3 atau lebih baru, jadi PHP 8.5.12 kompatibel. Laravel 13 mendukung PHP 8.3 sampai 8.5. PHP 8.5.12 adalah security release yang dirilis 21 Oktober 2026, jadi sebaiknya langsung pakai versi ini.

### 1. Install PHP 8.5.12

Unduh dari <https://www.php.net/downloads.php?os=windows>. Pilih:

- Versi PHP 8.5 (8.5.12), build **x64**
- **Non Thread Safe (NTS)**, cocok untuk CLI dan `php artisan serve`. Pilih Thread Safe hanya kalau PHP dipakai sebagai modul Apache.

Pastikan juga **Visual C++ Redistributable 2015-2022 x64** sudah terpasang, karena build PHP membutuhkannya.

Lalu ekstrak dan atur lewat PowerShell (sesuaikan nama file zip yang diunduh):

```powershell
Expand-Archive "$env:USERPROFILE\Downloads\php-8.5.12-nts-Win32-vs17-x64.zip" -DestinationPath C:\php

# Tambah ke PATH (user)
[Environment]::SetEnvironmentVariable("Path", [Environment]::GetEnvironmentVariable("Path","User") + ";C:\php", "User")

# Buat php.ini
Copy-Item C:\php\php.ini-development C:\php\php.ini
```

Buka `C:\php\php.ini`, lalu hapus tanda `;` di depan baris berikut:

```ini
extension_dir = "ext"
extension=curl
extension=fileinfo
extension=intl
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=pdo_sqlite
extension=sqlite3
extension=zip
```

Tutup lalu buka ulang terminal, kemudian cek:

```powershell
php -v
php -m
```

> Folder instalasi tidak harus `C:\php`. Kalau PHP diekstrak ke folder lain (misalnya `C:\app\php-8.5.12`), tambahkan folder tersebut ke variabel **Path** (bukan membuat variabel baru).

### 2. Install Composer

Unduh installer dari <https://getcomposer.org/download> dan jalankan. Saat diminta memilih PHP, arahkan ke `php.exe` hasil langkah 1 (contoh: `C:\php\php.exe`). Verifikasi:

```powershell
composer --version
```

### 3. Install Node.js

Unduh versi LTS dari <https://nodejs.org>, lalu cek:

```powershell
node -v
npm -v
```

---

## Menjalankan Proyek Setelah Clone

```powershell
git clone https://github.com/JunadilMuqorobin/IMPAL-Kelompok-06
cd IMPAL-Kelompok-06

composer install
copy .env.example .env
php artisan key:generate
php artisan migrate

npm install
npm run build
```

Jalankan server development:

```powershell
composer run dev
```

Atau hanya server PHP:

```powershell
php artisan serve
```

Buka <http://127.0.0.1:8000>. Cek versi dengan `php artisan --version`, hasilnya harus `Laravel Framework 13.x`.

> **Penting:** file `.env` tidak ikut di-commit dan tiap anggota membuat sendiri dari `.env.example`. Jangan pernah meng-commit `.env`.

---

## Alur Kerja Git

```powershell
git pull                      # ambil perubahan terbaru sebelum mulai kerja
git status                    # cek branch aktif dan perubahan
git add .
git commit -m "pesan commit"
git push
```

Kalau membuat branch baru, push pertama kali dengan:

```powershell
git push -u origin nama-branch
```

Kalau `git push` ditolak (`rejected` / `non-fast-forward`), jalankan `git pull` dulu, selesaikan konflik jika ada, lalu push lagi.
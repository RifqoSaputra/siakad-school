# SIAKAD School

Sistem akademik SMK Mutiara Bangsa 1 berbasis Laravel.

## Tech Stack
- Composer
- Node.js
- Laravel 12
- PHP 8.3+
- Laragon, XAMP, Docker
- MySQL/MariaDB

## Instalasi
1) Clone repository
```bash
git clone https://github.com/RifqoSaputra/siakad-school.git
cd siakad-school
```

2) Install dependencies PHP
```bash
composer install
```

3) Siapkan environment
```bash
cp .env.example .env
php artisan key:generate
```
Sesuaikan koneksi database di `.env`.
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siakad_school
DB_USERNAME=root
DB_PASSWORD=

```

4) Jalankan migrasi SIAKAD
```bash
php artisan migrate --path=database/migrations/SIAKAD/SCHOOL
```

5) Jalankan seeder SIAKAD
```bash
php artisan db:seed --class=Database\\Seeders\\SIAKAD\\SCHOOL\\SiakadSeeder
```

6) (Opsional) Jalankan seeder Nilai 
```bash
# Untuk melihat dummy nilai 10 DKV-1 (Submitted)
php artisan db:seed --class=Database\\Seeders\\SIAKAD\\SCHOOL\\Nilai\\NilaiSeeder
```

5) Jalankan aplikasi
```bash
php artisan serve
```
Akses: http://localhost:8000

## Login (Default)
Password default semua akun: `password`

Akun Email Utama (Dummy):
- Admin: `rizky.alamsyah@admin.mutiarabangsa.ac.id`
- Guru: `rina.ps@mutiarabangsa.ac.id`
- Ortu: `bambang.sugeng@gmail.com`

## Catatan Flow "Lupa Kata Sandi"
- Link reset ditampilkan sebagai button dummy (tanpa pengiriman email sungguhan).
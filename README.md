# SIAKAD School

Sistem akademik SMK Mutiara Bangsa 1 berbasis Laravel.

## Tech Stack
- Laravel 12
- PHP 8.3+
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

4) Jalankan migrasi SIAKAD
```bash
php artisan migrate --path=database/migrations/SIAKAD/SCHOOL
```

5) Jalankan seeder SIAKAD
```bash
php artisan db:seed --class=Database\\Seeders\\SIAKAD\\SCHOOL\\SiakadSeeder
```

6) Jalankan aplikasi
```bash
php artisan serve
```
Akses: http://localhost:8000

## Login (Default)
Password default semua akun: `password`

Akun Email (Dummy):
- Admin: `rizky.alamsyah@admin.mutiarabangsa.ac.id`
- Guru: `rina.ps@mutiarabangsa.ac.id`
- Ortu: `bambang.sugeng@gmail.com`

## Catatan Flow "Lupa Kata Sandi"
- Link reset ditampilkan sebagai button dummy (tanpa pengiriman email sungguhan).
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

## Catatan Lupa Kata Sandi
- Flow reset password menggunakan tabel `password_reset_tokens`.
- Link reset ditampilkan sebagai tombol dummy (tanpa pengiriman email sungguhan).

## Tips
- Jika melakukan perubahan data email/akun, jalankan ulang seeder terkait:
  - `Database\\Seeders\\SIAKAD\\SCHOOL\\AdminSeeder`
  - `Database\\Seeders\\SIAKAD\\SCHOOL\\GuruSeeder`
  - `Database\\Seeders\\SIAKAD\\SCHOOL\\OrtuSeeder`
  - `Database\\Seeders\\SIAKAD\\SCHOOL\\UserSeeder`

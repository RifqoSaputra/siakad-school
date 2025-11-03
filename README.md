# Siakad School

## Tujuan
Dokumentasi ini menjelaskan langkah-langkah untuk menyalin (clone) repository dan menjalankan migrasi serta *seeding* Laravel, memastikan skema dan data awal sistem sesuai dengan kebutuhan proyek.

---

## Prerequisites (Persyaratan Awal)
1.  **Git / GitHub CLI**
2.  **PHP 8.3**
3.  **Composer** (Package Manager PHP)
4.  **Laravel** (Framework)
5.  **Web Server & Database:**
    * Sistem Operasi: Docker, Laragon, XAMPP, atau Vagrant (Laravel Sail disarankan).
    * Database: **MySQL / MariaDB** (Sesuaikan dengan koneksi yang digunakan di `.env`).

---

## 🚀 Langkah-Langkah Instalasi dan Setup

### 1. Salin Repository (Clone Repo)
Lakukan *clone* repository GitHub ke folder lokal Anda:
```bash
git clone [https://github.com/RifqoSaputra/siakad-school.git](https://github.com/RifqoSaputra/siakad-school.git)
cd siakad-school
```

### 2. Instal Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Environement (.env)
```bash
cp .env.example .env
php artisan key:generate
```
PENTING: Buka file .env dan pastikan konfigurasi database diatur dengan benar, terutama bagian:
```bash
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=siakad_school
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

### 4. Buat database di local anda dengan nama siakad_school
```bash
siakad_school
```

### 5. Jalankan Migrasi & Seeding "siakad_school"
```bash
# 2. Jalankan migrasi khusus SIAKAD/SCHOOL untuk membuat semua tabel
php artisan migrate --path=database/migrations/SIAKAD/SCHOOL

# 3. Jalankan seeder utama SIAKAD/SCHOOL untuk mengisi data awal
php artisan db:seed --class=Database\\Seeders\\SIAKAD\\SCHOOL\\DatabaseSeeder

```

### 6. Jalankan program 
```bash
php artisan serve
```
Akses aplikasi di: http://localhost:8000

### Detail Login Admin (Default)
Gunakan kredensial berikut untuk login pertama kali:
```bash
Username: user1

Password: password
```
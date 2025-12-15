<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Utama: Pengumuman
        // (Sudah termasuk kolom 'status' dari file migrasi ke-3)
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->increments('id_pengumuman'); // Primary Key (Integer Unsigned)
            $table->string('judul', 150);
            $table->text('isi_pengumuman')->nullable();
            
            // Kolom Status (Langsung digabung di sini)
            $table->enum('status', ['draft', 'published'])->default('published');
            
            $table->enum('target_role', ['admin', 'guru', 'ortu', 'all'])->default('all');
            $table->unsignedInteger('id_admin');
            $table->timestamps();

            // Foreign Key
            $table->foreign('id_admin')
                ->references('users_id')
                ->on('users')
                ->onDelete('restrict');

            // INDEXING
            // Index 'status' penting karena sering dipakai untuk filter (WHERE status = 'published')
            $table->index('status'); 
            $table->index('target_role');
            $table->index('id_admin');
            
            // Tips: Jika sering filter status DAN role bersamaan, pertimbangkan composite index:
            // $table->index(['status', 'target_role']);
        });

        // 2. Tabel Pivot: Pengumuman User (Tracking Read/Unread)
        Schema::create('pengumuman_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pengumuman_id');
            $table->unsignedInteger('users_id');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('pengumuman_id')
                ->references('id_pengumuman') // Pastikan nama kolom PK sama dengan tabel pengumuman
                ->on('pengumuman')
                ->onDelete('cascade');

            $table->foreign('users_id')
                ->references('users_id')
                ->on('users')
                ->onDelete('cascade');

            // INDEXING
            // Mencegah duplikasi data user & pengumuman yang sama
            $table->unique(['pengumuman_id', 'users_id']); 
            
            // Optimasi query dashboard: "Tampilkan pengumuman yang BELUM DIBACA oleh USER X"
            // Daripada index terpisah, composite index lebih cepat untuk query WHERE users_id = ? AND is_read = ?
            $table->index(['users_id', 'is_read']); 
        });

        // 3. Tabel Attachments
        Schema::create('pengumuman_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('pengumuman_id');
            $table->string('nama_file');
            $table->string('path');
            $table->unsignedBigInteger('size'); // Dalam bytes
            $table->string('mime_type')->nullable();
            $table->timestamps();

            // Foreign Key
            $table->foreign('pengumuman_id')
                ->references('id_pengumuman')
                ->on('pengumuman')
                ->onDelete('cascade');

            // INDEXING
            // Index ini penting untuk mengambil semua lampiran milik 1 pengumuman dengan cepat
            $table->index('pengumuman_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop harus berurutan terbalik untuk menghindari error Foreign Key Constraint
        Schema::dropIfExists('pengumuman_attachments');
        Schema::dropIfExists('pengumuman_user');
        Schema::dropIfExists('pengumuman');
    }
};
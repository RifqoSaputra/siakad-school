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
        /**
         * =========================
         * 1. TABEL PENGUMUMAN
         * =========================
         */
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->bigIncrements('id_pengumuman');

            $table->string('judul', 200);
            $table->longText('isi_pengumuman');

            // Target & Status
            $table->enum('target_role', ['guru', 'ortu', 'semua'])
                ->default('semua')
                ->index();

            $table->enum('status', ['draft', 'scheduled', 'sent'])
                ->default('draft')
                ->index();

            // Penjadwalan
            $table->dateTime('scheduled_at')->nullable()->index();
            $table->dateTime('sent_at')->nullable();

            // Creator
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign Key
            $table->foreign('created_by')
                ->references('users_id')
                ->on('users')
                ->nullOnDelete();

            // Composite index untuk scheduler
            $table->index(['status', 'scheduled_at']);
        });

        /**
         * =========================
         * 2. TABEL PIVOT (READ STATUS)
         * =========================
         */
        Schema::create('pengumuman_user', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('pengumuman_id');
            $table->unsignedInteger('users_id');

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('pengumuman_id')
                ->references('id_pengumuman')
                ->on('pengumuman')
                ->cascadeOnDelete();

            $table->foreign('users_id')
                ->references('users_id')
                ->on('users')
                ->cascadeOnDelete();

            // Cegah duplikasi
            $table->unique(['pengumuman_id', 'users_id']);

            // Optimasi dashboard
            $table->index(['users_id', 'is_read']);
        });

        /**
         * =========================
         * 3. TABEL ATTACHMENTS
         * =========================
         */
        Schema::create('pengumuman_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('pengumuman_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size');
            $table->string('file_mime')->nullable();
            $table->timestamps();

            $table->foreign('pengumuman_id')
                ->references('id_pengumuman')
                ->on('pengumuman')
                ->cascadeOnDelete();

            $table->index('pengumuman_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman_attachments');
        Schema::dropIfExists('pengumuman_user');
        Schema::dropIfExists('pengumuman');
    }
};

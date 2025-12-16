<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Jika tabel belum ada (antisipasi instalasi baru), buat dengan skema terbaru.
        if (!Schema::hasTable('pengumuman')) {
            Schema::create('pengumuman', function (Blueprint $table) {
                $table->bigIncrements('id_pengumuman');
                $table->string('judul', 200);
                $table->text('isi_pengumuman');
                $table->enum('target_role', ['guru', 'ortu', 'semua'])->default('semua');
                $table->enum('status', ['draft', 'dijadwalkan', 'dikirim'])->default('draft');
                $table->dateTime('scheduled_at')->nullable();
                $table->dateTime('sent_at')->nullable();
                $table->unsignedInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('created_by')
                    ->references('users_id')
                    ->on('users')
                    ->onDelete('set null');

                $table->index('target_role');
                $table->index('status');
            });

            return;
        }

        // Normalisasi data lama sebelum mengubah tipe kolom ENUM.
        DB::table('pengumuman')
            ->whereIn('target_role', ['all', 'admin'])
            ->update(['target_role' => 'semua']);

        DB::table('pengumuman')
            ->where('status', 'published')
            ->update(['status' => 'dikirim']);

        // Ubah daftar pilihan ENUM tanpa membutuhkan doctrine/dbal (gunakan raw statement).
        DB::statement("ALTER TABLE pengumuman MODIFY target_role ENUM('guru','ortu','semua') NOT NULL DEFAULT 'semua'");
        DB::statement("ALTER TABLE pengumuman MODIFY status ENUM('draft','dijadwalkan','dikirim') NOT NULL DEFAULT 'draft'");

        if (!Schema::hasColumn('pengumuman', 'scheduled_at')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dateTime('scheduled_at')->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('pengumuman', 'sent_at')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dateTime('sent_at')->nullable()->after('scheduled_at');
            });
        }

        if (!Schema::hasColumn('pengumuman', 'created_by')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->unsignedInteger('created_by')->nullable()->after('sent_at');
                $table->foreign('created_by')
                    ->references('users_id')
                    ->on('users')
                    ->onDelete('set null');
            });

            DB::statement('UPDATE pengumuman SET created_by = COALESCE(created_by, id_admin) WHERE created_by IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pengumuman', 'created_by')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            });
        }

        if (Schema::hasColumn('pengumuman', 'sent_at')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dropColumn('sent_at');
            });
        }

        if (Schema::hasColumn('pengumuman', 'scheduled_at')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dropColumn('scheduled_at');
            });
        }

        if (Schema::hasTable('pengumuman')) {
            DB::statement("ALTER TABLE pengumuman MODIFY target_role ENUM('admin','guru','ortu','all') NOT NULL DEFAULT 'all'");
            DB::statement("ALTER TABLE pengumuman MODIFY status ENUM('draft','published') NOT NULL DEFAULT 'published'");
        }
    }
};

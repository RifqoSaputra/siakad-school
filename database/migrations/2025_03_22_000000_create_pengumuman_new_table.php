<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengumuman')) {
            Schema::create('pengumuman', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('judul', 200);
                $table->longText('isi_pengumuman');
                $table->enum('target_role', ['guru', 'ortu', 'semua'])->default('semua')->index();
                $table->enum('status', ['draft', 'scheduled', 'sent'])->default('draft')->index();
                $table->dateTime('scheduled_for')->nullable()->index();
                $table->dateTime('sent_at')->nullable();
                $table->foreignId('id_admin')
                    ->constrained('users', 'users_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
                $table->timestamps();
            });
        } else {
            Schema::table('pengumuman', function (Blueprint $table) {
                if (!Schema::hasColumn('pengumuman', 'judul')) {
                    $table->string('judul', 200)->after('id_pengumuman');
                }
                if (!Schema::hasColumn('pengumuman', 'isi_pengumuman')) {
                    $table->longText('isi_pengumuman')->nullable();
                }
                if (!Schema::hasColumn('pengumuman', 'target_role')) {
                    $table->enum('target_role', ['guru', 'ortu', 'semua'])->default('semua')->index();
                }
                if (!Schema::hasColumn('pengumuman', 'status')) {
                    $table->enum('status', ['draft', 'scheduled', 'sent'])->default('draft')->index();
                }
                if (!Schema::hasColumn('pengumuman', 'scheduled_for')) {
                    $table->dateTime('scheduled_for')->nullable()->index();
                }
                if (!Schema::hasColumn('pengumuman', 'sent_at')) {
                    $table->dateTime('sent_at')->nullable();
                }
                if (!Schema::hasColumn('pengumuman', 'id_admin')) {
                    $table->foreignId('id_admin')
                        ->nullable()
                        ->constrained('users', 'users_id')
                        ->cascadeOnUpdate()
                        ->restrictOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        // Only drop if this migration created the table; avoid clobbering existing legacy data
        if (Schema::hasTable('pengumuman') && Schema::hasColumn('pengumuman', 'id')) {
            Schema::dropIfExists('pengumuman');
        }
    }
};

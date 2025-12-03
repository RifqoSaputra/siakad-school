<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_akhir_semester', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('id_siswa');
            $table->unsignedInteger('mapel_id');
            $table->unsignedInteger('kelas_id');

            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('tahun_ajaran', 10);

            // Nilai Rapor Final (menggantikan nilai_akhir_presisi & nilai_akhir_cetak)
            $table->decimal('nilai_rapor', 4, 1)->nullable();

            // KKM yang berlaku saat nilai ini dicatat (untuk integritas historis)
            $table->unsignedTinyInteger('kkm')->nullable();

            $table->text('deskripsi')->nullable();

            // Kolom Audit Kustom (Menggantikan $table->timestamps();)
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Foreign Keys
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->foreign('mapel_id')->references('mapel_id')->on('mapel')->onDelete('cascade');
            $table->foreign('kelas_id')->references('kelas_id')->on('kelas')->onDelete('cascade');

            // Unique + Indexing Optimal
            $table->unique(['id_siswa', 'mapel_id', 'semester', 'tahun_ajaran'], 'unique_nilai_akhir');

            $table->index('id_siswa');
            $table->index('kelas_id');
            $table->index('mapel_id');
            $table->index('semester');
            $table->index('tahun_ajaran');
            $table->index(
                ['kelas_id', 'mapel_id', 'semester', 'tahun_ajaran'],
                'idx_kelas_mapel_sms_ta'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_akhir_semester');
    }
};

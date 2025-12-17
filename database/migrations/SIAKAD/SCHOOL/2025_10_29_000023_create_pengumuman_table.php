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
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->increments('id_pengumuman');
            $table->string('judul', 150);
            $table->text('isi_pengumuman')->nullable();
            $table->enum('target_role', ['admin', 'guru', 'ortu', 'all'])->default('all');
            $table->enum('status', ['draft', 'scheduled', 'sent', 'published'])->default('sent');
            $table->dateTime('scheduled_for')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->unsignedInteger('id_admin');
            $table->timestamps();

            $table->foreign('id_admin')
                ->references('users_id')
                ->on('users')
                ->onDelete('restrict');

            $table->index('target_role');
            $table->index('status');
            $table->index('scheduled_for');
            $table->index('sent_at');
            $table->index('id_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};

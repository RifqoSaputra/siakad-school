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
        if (Schema::hasTable('pengumuman_attachments')) {
            return;
        }

        Schema::create('pengumuman_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('pengumuman_id');
            $table->string('nama_file');
            $table->string('path');
            $table->unsignedBigInteger('size');
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->foreign('pengumuman_id')
                ->references('id_pengumuman')
                ->on('pengumuman')
                ->onDelete('cascade');

            $table->index('pengumuman_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman_attachments');
    }
};

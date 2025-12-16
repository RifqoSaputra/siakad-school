<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengumuman_attachments')) {
            Schema::create('pengumuman_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pengumuman_id')
                    ->constrained('pengumuman')
                    ->cascadeOnDelete();
                $table->string('file_name');
                $table->string('file_path');
                $table->unsignedInteger('file_size');
                $table->string('file_mime');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman_attachments');
    }
};

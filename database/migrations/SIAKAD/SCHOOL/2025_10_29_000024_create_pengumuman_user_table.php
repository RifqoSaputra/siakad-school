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
        if (Schema::hasTable('pengumuman_user')) {
            return;
        }

        Schema::create('pengumuman_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pengumuman_id');
            $table->unsignedInteger('users_id');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('pengumuman_id')
                ->references('id_pengumuman')
                ->on('pengumuman')
                ->onDelete('cascade');

            $table->foreign('users_id')
                ->references('users_id')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['pengumuman_id', 'users_id']);
            $table->index('users_id');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman_user');
    }
};

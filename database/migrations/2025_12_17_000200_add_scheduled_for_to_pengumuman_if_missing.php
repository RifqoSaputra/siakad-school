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
        if (!Schema::hasColumn('pengumuman', 'scheduled_for')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dateTime('scheduled_for')->nullable()->after('status')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pengumuman', 'scheduled_for')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dropColumn('scheduled_for');
            });
        }
    }
};

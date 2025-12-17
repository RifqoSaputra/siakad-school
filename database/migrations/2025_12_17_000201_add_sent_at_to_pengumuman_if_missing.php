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
        if (!Schema::hasColumn('pengumuman', 'sent_at')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dateTime('sent_at')->nullable()->after('scheduled_for');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pengumuman', 'sent_at')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dropColumn('sent_at');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Unique constraint: one absensi per user per dapur per day
            $table->unique(['user_id', 'dapur_id', 'created_at'], 'absensis_user_dapur_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('absensis_user_dapur_date_unique');
        });
    }
};

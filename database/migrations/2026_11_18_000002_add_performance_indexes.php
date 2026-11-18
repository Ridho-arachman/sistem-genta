<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laporans akan tumbuh cepat (tiap pengawas tiap hari); dapur_user
     * sering dicari dari sisi user (akses pengawas).
     */
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->index(['dapur_id', 'status']);
            $table->index('created_by');
            $table->index('created_at');
        });

        Schema::table('dapur_user', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropIndex(['dapur_id', 'status']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('dapur_user', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};

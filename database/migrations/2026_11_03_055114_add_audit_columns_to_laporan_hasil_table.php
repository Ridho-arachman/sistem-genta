<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_hasil', function (Blueprint $table) {
            $table->foreignUlid('created_by')->nullable()->constrained('users')->after('indikator');
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_hasil', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};

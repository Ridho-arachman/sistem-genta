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
        Schema::table('dapurs', function (Blueprint $table) {
            $table->string('kecamatan', 100)->nullable()->after('alamat');
            $table->string('kabupaten_kota', 100)->nullable()->after('kecamatan');
            $table->string('provinsi', 100)->nullable()->after('kabupaten_kota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dapurs', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'kabupaten_kota', 'provinsi']);
        });
    }
};
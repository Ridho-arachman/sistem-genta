<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cctvs', function (Blueprint $table) {
            $table->string('section')->nullable()->after('rtsp_url'); // Area: 'Gudang Bahan', 'Area Masak', dll
            $table->unsignedInteger('camera_number')->nullable()->after('section'); // Urutan kamera
        });
    }

    public function down(): void
    {
        Schema::table('cctvs', function (Blueprint $table) {
            $table->dropColumn(['section', 'camera_number']);
        });
    }
};

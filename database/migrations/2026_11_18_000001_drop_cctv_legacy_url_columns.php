<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ip_address dan hls_url sudah tidak dipakai: alamat kamera ada di
     * rtsp_url, URL preview diturunkan dari CCTV_HLS_BASE + stream_key.
     */
    public function up(): void
    {
        Schema::table('cctvs', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'hls_url']);
        });
    }

    public function down(): void
    {
        Schema::table('cctvs', function (Blueprint $table) {
            $table->string('ip_address')->nullable();
            $table->string('hls_url')->nullable()->after('rtsp_url');
        });
    }
};

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
        Schema::create('laporan_hasil', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('laporan_id')->constrained()->onDelete('cascade');
            $table->string('indikator'); // nama indikator: Kebersihan, Penyimpanan, dll
            $table->enum('hasil', ['diperiksa', 'temuan_sedang', 'temuan_kritis']);
            $table->timestamps();
            $table->unique(['laporan_id', 'indikator']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_hasil');
    }
};

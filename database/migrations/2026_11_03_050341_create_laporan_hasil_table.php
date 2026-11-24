<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_hasil', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('laporan_id')->constrained()->cascadeOnDelete();
            $table->string('indikator');
            $table->enum('hasil', ['diperiksa', 'temuan_sedang', 'temuan_kritis']);
            $table->foreignUlid('created_by')->nullable()->constrained('users');
            $table->foreignUlid('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['laporan_id', 'indikator']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_hasil');
    }
};

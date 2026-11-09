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
        Schema::create('laporans', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('dapur_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['diperiksa', 'temuan', 'tindak lanjut', 'selesai'])->default('diperiksa');
            $table->text('catatan')->nullable();
            $table->string('selfie_photo')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};

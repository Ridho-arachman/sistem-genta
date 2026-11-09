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
        Schema::dropIfExists('laporan_photos');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('laporan_photos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('laporan_id')->constrained()->onDelete('cascade');
            $table->string('photo_path');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('address')->nullable();
            $table->string('indikator')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users');
            $table->foreignUlid('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }
};

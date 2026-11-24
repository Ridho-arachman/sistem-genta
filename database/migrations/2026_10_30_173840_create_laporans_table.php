<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('dapur_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['diperiksa', 'temuan', 'tindak lanjut', 'selesai'])->default('diperiksa');
            $table->text('catatan')->nullable();
            $table->text('selfie_photo')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('address')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users');
            $table->foreignUlid('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['dapur_id', 'status']);
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};

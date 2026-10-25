<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sp_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pengawas yang kena SP
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // Admin yg buat SP
            $table->unsignedTinyInteger('level'); // 1, 2, 3
            $table->text('reason');
            $table->enum('status', ['aktif', 'dicabut', 'selesai'])->default('aktif');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['admin_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sp_warnings');
    }
};

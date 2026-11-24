<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cctvs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('dapur_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('rtsp_url')->nullable();
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('offline');
            $table->string('section')->nullable();
            $table->unsignedInteger('camera_number')->nullable();
            $table->ulid('stream_key')->nullable()->unique();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->index(['dapur_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctvs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add dapur_id to users (for kepala_dapur single dapur)
        Schema::table('users', function (Blueprint $table) {
            if (DB::getDriverName() === 'sqlite') {
                $table->unsignedBigInteger('dapur_id')->nullable()->after('role');
            } else {
                $table->foreignId('dapur_id')->nullable()->constrained('dapurs')->onDelete('set null')->after('role');
            }
        });

        // Pivot table for pengawas multi-dapur
        Schema::create('dapur_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dapur_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['dapur_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dapur_user');
        Schema::table('users', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['dapur_id']);
            }
            $table->dropColumn('dapur_id');
        });
    }
};

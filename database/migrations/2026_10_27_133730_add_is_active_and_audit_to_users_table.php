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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('dapur_id');
            $table->timestamp('deactivated_at')->nullable()->after('is_active');
            $table->unsignedBigInteger('deactivated_by')->nullable()->after('deactivated_at');
            $table->unsignedBigInteger('created_by')->nullable()->after('deactivated_by');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropColumn(['is_active', 'deactivated_at', 'deactivated_by', 'created_by', 'updated_by']);
        });
    }
};

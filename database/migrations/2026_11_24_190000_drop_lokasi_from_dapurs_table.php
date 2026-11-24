<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dapurs', 'lokasi')) {
            return;
        }

        Schema::table('dapurs', function (Blueprint $table) {
            $table->dropColumn('lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('dapurs', function (Blueprint $table) {
            $table->text('lokasi')->nullable();
        });
    }
};

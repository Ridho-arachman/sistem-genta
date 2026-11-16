<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cctvs', function (Blueprint $table) {
            $table->ulid('stream_key')->nullable()->unique()->after('id');
            $table->timestamp('last_checked_at')->nullable()->after('status');
            $table->index(['dapur_id', 'status']);
        });

        // Backfill kamera lama: stream_key dipakai sebagai nama path di MediaMTX.
        foreach (DB::table('cctvs')->whereNull('stream_key')->pluck('id') as $id) {
            DB::table('cctvs')->where('id', $id)->update(['stream_key' => (string) Str::ulid()]);
        }
    }

    public function down(): void
    {
        Schema::table('cctvs', function (Blueprint $table) {
            $table->dropUnique(['stream_key']);
            $table->dropIndex(['dapur_id', 'status']);
            $table->dropColumn(['stream_key', 'last_checked_at']);
        });
    }
};

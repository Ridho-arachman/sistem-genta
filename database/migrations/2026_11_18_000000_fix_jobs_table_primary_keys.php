<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Queue database Laravel memakai insertGetId(), jadi kolom id harus
     * auto-increment. Migrasi awal sempat memakai ULID sehingga job tidak
     * pernah bisa masuk.
     */
    public function up(): void
    {
        $this->rebuild('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedSmallInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        $this->rebuild('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->string('connection');
            $table->string('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();

            $table->index(['connection', 'queue', 'failed_at']);
        });
    }

    public function down(): void
    {
        // Perubahan tipe primary key tidak dikembalikan.
    }

    /**
     * @param  Closure(Blueprint): void  $definition
     */
    private function rebuild(string $table, Closure $definition): void
    {
        if (! Schema::hasTable($table) || Schema::getColumnType($table, 'id') === 'bigint') {
            return;
        }

        if (DB::table($table)->exists()) {
            Log::warning("Tabel {$table} berisi data, primary key tidak diubah otomatis. Kosongkan tabel lalu jalankan ulang migrasi ini.");

            return;
        }

        Schema::drop($table);
        Schema::create($table, $definition);
    }
};

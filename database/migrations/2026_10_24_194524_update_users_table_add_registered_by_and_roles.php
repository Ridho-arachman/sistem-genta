<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add registered_by first
            $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('set null')->after('email');
        });

        // For SQLite (testing), we need to recreate the column
        // For PostgreSQL (production), we can use ALTER COLUMN
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN TYPE, so we recreate
            DB::statement("CREATE TABLE users_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                role VARCHAR(20) NOT NULL DEFAULT 'pengawas' CHECK (role IN ('admin', 'pengawas', 'manajerial', 'kepala_dapur')),
                registered_by INTEGER NULL REFERENCES users(id) ON DELETE SET NULL,
                password VARCHAR(255) NOT NULL,
                remember_token VARCHAR(100) NULL,
                email_verified_at DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )");

            DB::statement('INSERT INTO users_new (id, name, email, role, registered_by, password, remember_token, email_verified_at, created_at, updated_at)
                SELECT id, name, email, role, registered_by, password, remember_token, email_verified_at, created_at, updated_at FROM users');

            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');
        } else {
            // PostgreSQL
            DB::statement('ALTER TABLE users ALTER COLUMN role TYPE varchar(20) USING role::varchar(20)');
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'pengawas'");
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'pengawas', 'manajerial', 'kepala_dapur'))");
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['registered_by']);
            $table->dropColumn('registered_by');
        });

        if (DB::getDriverName() === 'sqlite') {
            DB::statement("CREATE TABLE users_old (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                role VARCHAR(20) NOT NULL DEFAULT 'pengawas' CHECK (role IN ('admin', 'pengawas')),
                password VARCHAR(255) NOT NULL,
                remember_token VARCHAR(100) NULL,
                email_verified_at DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )");

            DB::statement('INSERT INTO users_old (id, name, email, role, password, remember_token, email_verified_at, created_at, updated_at)
                SELECT id, name, email, role, password, remember_token, email_verified_at, created_at, updated_at FROM users');

            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_old RENAME TO users');
        } else {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement('ALTER TABLE users ALTER COLUMN role TYPE varchar(20) USING role::varchar(20)');
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'pengawas'");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'pengawas'))");
        }
    }
};

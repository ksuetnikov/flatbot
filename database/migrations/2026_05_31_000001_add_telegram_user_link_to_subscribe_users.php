<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            DB::statement('
                CREATE TABLE telegram_users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    telegram_id INTEGER,
                    first_name VARCHAR,
                    username VARCHAR,
                    registration_key VARCHAR,
                    state VARCHAR NOT NULL DEFAULT \'main_menu\',
                    is_admin TINYINT(1) NOT NULL DEFAULT 0,
                    created_at DATETIME
                )
            ');

            DB::statement('
                INSERT INTO telegram_users_new (
                    id, telegram_id, first_name, username, registration_key, state, is_admin, created_at
                )
                SELECT
                    id,
                    CASE WHEN telegram_id = 0 THEN NULL ELSE telegram_id END,
                    first_name,
                    username,
                    registration_key,
                    state,
                    is_admin,
                    created_at
                FROM telegram_users
            ');

            DB::statement('DROP TABLE telegram_users');
            DB::statement('ALTER TABLE telegram_users_new RENAME TO telegram_users');
            DB::statement(
                'CREATE UNIQUE INDEX telegram_users_telegram_id_unique ON telegram_users (telegram_id) WHERE telegram_id IS NOT NULL'
            );

            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            Schema::table('telegram_users', function (Blueprint $table) {
                $table->unsignedBigInteger('telegram_id')->nullable()->change();
            });
        }

        Schema::table('subscribe_users', function (Blueprint $table) {
            $table
                ->foreignId('telegram_user_id')
                ->nullable()
                ->after('object_id')
                ->constrained('telegram_users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subscribe_users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('telegram_user_id');
        });

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            DB::statement('
                CREATE TABLE telegram_users_old (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    telegram_id INTEGER NOT NULL,
                    first_name VARCHAR,
                    username VARCHAR,
                    registration_key VARCHAR,
                    state VARCHAR NOT NULL DEFAULT \'main_menu\',
                    is_admin TINYINT(1) NOT NULL DEFAULT 0,
                    created_at DATETIME
                )
            ');

            DB::statement('
                INSERT INTO telegram_users_old (
                    id, telegram_id, first_name, username, registration_key, state, is_admin, created_at
                )
                SELECT
                    id,
                    COALESCE(telegram_id, 0),
                    first_name,
                    username,
                    registration_key,
                    state,
                    is_admin,
                    created_at
                FROM telegram_users
            ');

            DB::statement('DROP TABLE telegram_users');
            DB::statement('ALTER TABLE telegram_users_old RENAME TO telegram_users');
            DB::statement('CREATE UNIQUE INDEX telegram_users_telegram_id_unique ON telegram_users (telegram_id)');

            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            Schema::table('telegram_users', function (Blueprint $table) {
                $table->unsignedBigInteger('telegram_id')->nullable(false)->change();
            });
        }
    }
};

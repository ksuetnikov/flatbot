<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TelegramUserSeeder extends Seeder
{
    /**
     * Тестовый админский ключ регистрации (как в goBot/migrations/init.sql).
     */
    public function run(): void
    {
        DB::table('telegram_users')->updateOrInsert(
            ['telegram_id' => 0],
            [
                'registration_key' => '43',
                'is_admin' => true,
                'username' => 'kir_10rus',
                'first_name' => null,
                'state' => 'main_menu',
                'created_at' => now(),
            ]
        );
    }
}

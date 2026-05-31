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
            ['registration_key' => '43'],
            [
                'telegram_id' => null,
                'is_admin' => true,
                'username' => 'kir_10rus',
                'first_name' => null,
                'state' => 'main_menu',
                'created_at' => now(),
            ]
        );
    }
}

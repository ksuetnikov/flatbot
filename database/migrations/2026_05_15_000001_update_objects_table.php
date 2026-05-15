<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::table('objects', function (Blueprint $table) {
            $table
                ->text('user_code')
                ->nullable()
                ->comment('Код пользователя в Телеграм');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('objects', function (Blueprint $table) {
            $table->dropColumn('user_code');
        });
    }
};

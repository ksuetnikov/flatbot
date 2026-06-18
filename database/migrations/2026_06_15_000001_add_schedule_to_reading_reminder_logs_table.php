<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_reminder_logs', function (Blueprint $table) {
            $table->dropUnique('reading_reminder_logs_unique');
        });

        DB::table('reading_reminder_logs')->delete();

        Schema::table('reading_reminder_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('schedule_day')->after('reminder_type');
            $table->unsignedTinyInteger('schedule_hour')->after('schedule_day');
            $table->unsignedTinyInteger('schedule_minute')->after('schedule_hour');

            $table->unique(
                [
                    'object_id',
                    'subscribe_user_id',
                    'period_year',
                    'period_month',
                    'reminder_type',
                    'schedule_day',
                    'schedule_hour',
                    'schedule_minute',
                ],
                'reading_reminder_logs_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('reading_reminder_logs', function (Blueprint $table) {
            $table->dropUnique('reading_reminder_logs_unique');
            $table->dropColumn(['schedule_day', 'schedule_hour', 'schedule_minute']);
        });

        Schema::table('reading_reminder_logs', function (Blueprint $table) {
            $table->unique(
                ['object_id', 'subscribe_user_id', 'period_year', 'period_month', 'reminder_type'],
                'reading_reminder_logs_unique'
            );
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_reminder_logs', function (Blueprint $table) {
            $table->comment('Лог отправленных Telegram-напоминаний о показаниях');
            $table->id();
            $table->foreignId('object_id')->constrained('objects')->cascadeOnDelete();
            $table->foreignId('subscribe_user_id')->constrained('subscribe_users')->cascadeOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->string('reminder_type', 16)->comment('due или overdue');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(
                ['object_id', 'subscribe_user_id', 'period_year', 'period_month', 'reminder_type'],
                'reading_reminder_logs_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_reminder_logs');
    }
};

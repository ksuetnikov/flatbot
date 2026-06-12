<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ResetApplicationCommand extends Command
{
    protected $signature = 'app:reset
                            {--force : Пропустить подтверждение}
                            {--seed-telegram : Создать слот регистрации Telegram-бота после сброса}
                            {--keep-files : Не удалять загруженные файлы счётчиков}';

    protected $description = 'Сбросить приложение в пустое состояние (БД, файлы, кэш)';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirmReset()) {
            $this->warn('Отменено.');

            return self::FAILURE;
        }

        $this->info('Сброс базы данных...');

        $migrateOptions = ['--force' => true];
        if ($this->option('seed-telegram')) {
            $migrateOptions['--seeder'] = 'TelegramUserSeeder';
        }

        Artisan::call('migrate:fresh', $migrateOptions, $this->output);

        if (! $this->option('keep-files')) {
            $this->cleanUploadedFiles();
        }

        $this->info('Очистка кэша приложения...');
        Artisan::call('cache:clear', [], $this->output);
        Artisan::call('config:clear', [], $this->output);
        Artisan::call('view:clear', [], $this->output);

        $this->newLine();
        $this->info('Приложение сброшено в пустое состояние.');
        $this->line('  • Зарегистрируйте пользователя веб-панели через /register');

        if (! $this->option('seed-telegram')) {
            $this->line('  • Для бота: php artisan db:seed --class=TelegramUserSeeder');
        }

        $this->line('  • Перезапустите goBot, если он запущен');

        return self::SUCCESS;
    }

    private function confirmReset(): bool
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        $this->warn('Эта команда удалит ВСЕ данные приложения.');
        $this->line("  База данных: {$connection} ({$database})");

        if (app()->environment('production')) {
            $this->error('Окружение: production');
        }

        return $this->confirm('Продолжить?', false);
    }

    private function cleanUploadedFiles(): void
    {
        $this->info('Удаление загруженных файлов...');

        $disk = Storage::disk('public');

        if ($disk->exists('property-files')) {
            $disk->deleteDirectory('property-files');
        }

        $disk->makeDirectory('property-files');
    }
}

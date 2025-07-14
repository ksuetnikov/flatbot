<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Kirill Suetnikov
 */
return new class extends Migration {
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('objects', function (Blueprint $table) {
            $table->comment('Объекты');
            $table->id();
            $table
                ->text('name')
                ->comment('Наименование объекта');
            $table
                ->text('description')
                ->nullable()
                ->comment('Описание объекта');
            $table
                ->foreignId('user_id')
                ->nullable()
                ->comment('Идентификатор пользователя, создавшего данный объект')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('order', 30, 10);
            $table
                ->timestamp('notification_date')
                ->nullable()
                ->comment('Временная метка уведомления пользователя');
            $table
                ->timestamp('created_at')
                ->nullable()
                ->comment('Временная метка создания записи');
            $table
                ->timestamp('updated_at')
                ->nullable()
                ->comment('Временная метка обновления записи');
            $table
                ->timestamp('deleted_at')
                ->nullable()
                ->comment('Временная метка мягкого удаления записи');

            $table->index('user_id');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('objects');
    }
};

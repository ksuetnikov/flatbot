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
        Schema::create('object_properties', function (Blueprint $table) {
            $table->comment('Свойства объектов');
            $table->id();
            $table
                ->text('name')
                ->comment('Наименование свойства');
            $table
                ->text('code')
                ->nullable()
                ->comment('Описание подписчика');
            $table
                ->text('description')
                ->nullable()
                ->comment('Описание подписчика');
            $table
                ->foreignId('object_id')
                ->nullable()
                ->comment('Идентификатор объекта')
                ->constrained('objects')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('order', 30, 10);
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

            $table->index('object_id');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('object_properties');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('object_property_files', function (Blueprint $table) {
            $table->comment('Вложения к счётчикам (свойствам объекта): фото и PDF');
            $table->id();
            $table
                ->foreignId('object_property_id')
                ->constrained('object_properties')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 127)->nullable();
            $table->unsignedBigInteger('size')->comment('Размер в байтах');
            $table->timestamps();

            $table->index('object_property_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('object_property_files');
    }
};

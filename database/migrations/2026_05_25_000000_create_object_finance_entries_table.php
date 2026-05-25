<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('object_finance_entries', function (Blueprint $table) {
            $table->comment('Доходы и расходы объекта по месяцам');
            $table->id();
            $table
                ->foreignId('object_id')
                ->constrained('objects')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('year')->comment('Год');
            $table->unsignedTinyInteger('month')->comment('Месяц 1–12');
            $table->string('type', 16)->comment('income | expense');
            $table->decimal('amount', 14, 2)->comment('Сумма');
            $table->string('comment', 500)->nullable()->comment('Комментарий');
            $table->timestamps();

            $table->index(['object_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('object_finance_entries');
    }
};

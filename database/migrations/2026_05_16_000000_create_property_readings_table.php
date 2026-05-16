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
        Schema::create('property_readings', function (Blueprint $table) {
            $table->comment('История показаний счётчиков');
            $table->id();
            $table
                ->foreignId('object_property_id')
                ->constrained('object_properties')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('value', 20, 4)->comment('Показание');
            $table->timestamp('recorded_at')->comment('Дата и время передачи');
            $table->timestamps();

            $table->index(['object_property_id', 'recorded_at']);
        });

        $this->migrateExistingValues();
    }

    public function down(): void
    {
        Schema::dropIfExists('property_readings');
    }

    private function migrateExistingValues(): void
    {
        $properties = DB::table('object_properties')
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->get(['id', 'value', 'updated_at', 'created_at']);

        $now = now();

        foreach ($properties as $property) {
            $recordedAt = $property->updated_at ?? $property->created_at ?? $now;

            DB::table('property_readings')->insert([
                'object_property_id' => $property->id,
                'value' => $property->value,
                'recorded_at' => $recordedAt,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};

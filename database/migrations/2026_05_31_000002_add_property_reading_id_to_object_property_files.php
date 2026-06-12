<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('object_property_files', function (Blueprint $table) {
            $table
                ->foreignId('property_reading_id')
                ->nullable()
                ->after('object_property_id')
                ->constrained('property_readings')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique('property_reading_id');
        });
    }

    public function down(): void
    {
        Schema::table('object_property_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('property_reading_id');
        });
    }
};

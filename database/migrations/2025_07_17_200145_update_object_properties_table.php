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
        Schema::table('object_properties', function (Blueprint $table) {
            $table
                ->text('value')
                ->nullable()
                ->comment('Значение');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('object_properties', function (Blueprint $table) {
            $table->dropColumn('value');
        });
    }
};

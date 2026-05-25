<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('object_finance_entries', function (Blueprint $table) {
            $table->unsignedTinyInteger('day')
                ->default(1)
                ->after('month')
                ->comment('День 1–31');
        });
    }

    public function down(): void
    {
        Schema::table('object_finance_entries', function (Blueprint $table) {
            $table->dropColumn('day');
        });
    }
};

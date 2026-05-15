<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telegram_id')->unique();
            $table->string('first_name')->nullable();
            $table->string('username')->nullable();
            $table->string('registration_key')->nullable();
            $table->string('state')->default('main_menu');
            $table->boolean('is_admin')->default(false);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};

<?php

declare(strict_types=1);

namespace Database\Factories\Object;

use App\Models\Object\ObjectModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<ObjectModel>
 */
class ObjectModelFactory extends Factory
{
    /**
     * @return array<string, ObjectModel>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => 'Описание',
            'user_id' => 1,
            'order' => fake()->unique()->randomFloat(null, 1, 100),
            'created_at' => Carbon::now()->toISOString(),
            'updated_at' => Carbon::now()->toISOString(),
            'notification_date' => Carbon::now()->toISOString(),
        ];
    }
}

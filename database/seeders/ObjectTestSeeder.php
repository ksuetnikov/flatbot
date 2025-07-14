<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Object\ObjectModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @author Kirill Suetnikov
 */
class ObjectTestSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ObjectModel::factory()->create();
        ObjectModel::factory()->create();
        ObjectModel::factory()->create();
    }

}

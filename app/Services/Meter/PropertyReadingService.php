<?php

declare(strict_types=1);

namespace App\Services\Meter;

use App\Models\Object\ObjectProperty;
use App\Models\Object\PropertyReading;
use Illuminate\Support\Carbon;

class PropertyReadingService
{
    public function record(int $objectPropertyId, float $value, ?Carbon $recordedAt = null): PropertyReading
    {
        $property = ObjectProperty::query()->findOrFail($objectPropertyId);
        $recordedAt ??= now();

        $reading = PropertyReading::query()->create([
            'object_property_id' => $objectPropertyId,
            'value' => $value,
            'recorded_at' => $recordedAt,
        ]);

        $property->value = (string) $value;
        $property->save();

        return $reading;
    }
}

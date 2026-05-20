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
        ObjectProperty::query()->findOrFail($objectPropertyId);
        $recordedAt ??= now();

        $reading = PropertyReading::query()->create([
            'object_property_id' => $objectPropertyId,
            'value' => $value,
            'recorded_at' => $recordedAt,
        ]);

        $this->syncLatestValueToProperty($objectPropertyId);

        return $reading;
    }

    public function updateReading(PropertyReading $reading, float $value, Carbon $recordedAt): PropertyReading
    {
        $reading->update([
            'value' => $value,
            'recorded_at' => $recordedAt,
        ]);
        $this->syncLatestValueToProperty($reading->object_property_id);

        return $reading->fresh();
    }

    private function syncLatestValueToProperty(int $objectPropertyId): void
    {
        $property = ObjectProperty::query()->find($objectPropertyId);
        if ($property === null) {
            return;
        }

        $latest = PropertyReading::query()
            ->where('object_property_id', $objectPropertyId)
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->first();

        $property->value = $latest !== null ? (string) $latest->value : null;
        $property->save();
    }
}

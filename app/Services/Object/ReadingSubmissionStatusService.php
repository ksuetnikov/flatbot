<?php

declare(strict_types=1);

namespace App\Services\Object;

use App\Models\Object\ObjectModel;
use App\Models\Object\ObjectProperty;
use App\Models\Object\PropertyReading;
use Illuminate\Support\Carbon;

class ReadingSubmissionStatusService
{
    /**
     * @return array<string, mixed>
     */
    public function forObject(ObjectModel $object, ?Carbon $at = null): array
    {
        $at ??= now();
        $periodStart = $at->copy()->startOfMonth();
        $periodEnd = $at->copy()->endOfMonth();

        $object->loadMissing([
            'properties',
            'subscribers.telegramUser',
        ]);

        $properties = $object->properties;
        $propertyIds = $properties->pluck('id');

        $readingsInPeriod = PropertyReading::query()
            ->whereIn('object_property_id', $propertyIds)
            ->whereBetween('recorded_at', [$periodStart, $periodEnd])
            ->get()
            ->groupBy('object_property_id');

        $propertyStatuses = $properties->map(static function (ObjectProperty $property) use ($readingsInPeriod) {
            /** @var \Illuminate\Support\Collection<int, PropertyReading> $readings */
            $readings = $readingsInPeriod->get($property->id, collect());
            $latestInPeriod = $readings->sortByDesc('recorded_at')->first();

            return [
                'id' => $property->id,
                'name' => $property->name,
                'submitted_this_month' => $readings->isNotEmpty(),
                'latest_in_period' => $latestInPeriod ? [
                    'value' => (string) $latestInPeriod->value,
                    'recorded_at' => $latestInPeriod->recorded_at,
                ] : null,
            ];
        })->values()->all();

        $submittedCount = collect($propertyStatuses)->where('submitted_this_month', true)->count();
        $totalCount = count($propertyStatuses);

        $missingProperties = collect($propertyStatuses)
            ->reject(static fn (array $row) => $row['submitted_this_month'])
            ->pluck('name')
            ->values()
            ->all();

        $subscribers = $object->subscribers->map(static fn ($subscriber) => [
            'id' => $subscriber->id,
            'name' => $subscriber->name,
            'is_registered' => $subscriber->telegramUser?->isRegistered() ?? false,
        ])->values()->all();

        return [
            'period_label' => $at->translatedFormat('F Y'),
            'is_complete' => $totalCount > 0 && $submittedCount === $totalCount,
            'submitted_count' => $submittedCount,
            'total_count' => $totalCount,
            'missing_properties' => $missingProperties,
            'properties' => $propertyStatuses,
            'subscribers' => $subscribers,
        ];
    }
}

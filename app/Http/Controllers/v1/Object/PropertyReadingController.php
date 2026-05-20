<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StorePropertyReadingRequest;
use App\Http\Requests\Object\UpdatePropertyReadingRequest;
use App\Models\Object\ObjectModel;
use App\Models\Object\ObjectProperty;
use App\Models\Object\PropertyReading;
use App\Services\Meter\PropertyReadingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class PropertyReadingController extends Controller
{
    public function __construct(
        private readonly PropertyReadingService $propertyReadingService,
    ) {
    }

    public function store(
        StorePropertyReadingRequest $request,
        ObjectModel $object,
        int $propertyId,
    ): RedirectResponse {
        $this->assertPropertyBelongsToObject($object, $propertyId);

        $validated = $request->validated();
        $this->propertyReadingService->record(
            objectPropertyId: $propertyId,
            value: (float) $validated['value'],
            recordedAt: Carbon::parse($validated['recorded_at']),
        );

        return redirect()->back()->with('success', 'Показание добавлено');
    }

    public function update(
        UpdatePropertyReadingRequest $request,
        ObjectModel $object,
        int $propertyId,
        int $readingId,
    ): RedirectResponse {
        $this->assertPropertyBelongsToObject($object, $propertyId);

        $reading = PropertyReading::query()
            ->where('object_property_id', $propertyId)
            ->findOrFail($readingId);

        $validated = $request->validated();
        $this->propertyReadingService->updateReading(
            reading: $reading,
            value: (float) $validated['value'],
            recordedAt: Carbon::parse($validated['recorded_at']),
        );

        return redirect()->back()->with('success', 'Показание обновлено');
    }

    private function assertPropertyBelongsToObject(ObjectModel $object, int $propertyId): void
    {
        ObjectProperty::query()
            ->where('object_id', $object->getKey())
            ->whereKey($propertyId)
            ->firstOrFail();
    }
}

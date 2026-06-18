<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\ExportPeriodRequest;
use App\Http\Requests\Object\StorePropertyReadingRequest;
use App\Http\Requests\Object\UpdatePropertyReadingRequest;
use App\Models\Object\ObjectModel;
use App\Models\Object\ObjectProperty;
use App\Models\Object\PropertyReading;
use App\Services\Export\PropertyReadingsExportService;
use App\Services\Meter\PropertyReadingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyReadingController extends Controller
{
    public function __construct(
        private readonly PropertyReadingService $propertyReadingService,
        private readonly PropertyReadingsExportService $propertyReadingsExportService,
    ) {
    }

    public function export(
        ExportPeriodRequest $request,
        ObjectModel $object,
        int $propertyId,
    ): StreamedResponse {
        $property = $this->findPropertyForObject($object, $propertyId);

        return $this->propertyReadingsExportService->export(
            $object,
            $property,
            $request->toExportPeriod(),
        );
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

    public function destroy(
        ObjectModel $object,
        int $propertyId,
        int $readingId,
    ): RedirectResponse {
        $this->assertPropertyBelongsToObject($object, $propertyId);

        $reading = PropertyReading::query()
            ->where('object_property_id', $propertyId)
            ->findOrFail($readingId);

        $this->propertyReadingService->deleteReading($reading);

        return redirect()->back()->with('success', 'Показание удалено');
    }

    private function assertPropertyBelongsToObject(ObjectModel $object, int $propertyId): void
    {
        $this->findPropertyForObject($object, $propertyId);
    }

    private function findPropertyForObject(ObjectModel $object, int $propertyId): ObjectProperty
    {
        return ObjectProperty::query()
            ->where('object_id', $object->getKey())
            ->whereKey($propertyId)
            ->firstOrFail();
    }
}

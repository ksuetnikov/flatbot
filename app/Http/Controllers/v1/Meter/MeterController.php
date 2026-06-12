<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Meter;

use App\Http\Controllers\Controller;
use App\Models\Object\PropertyReading;
use App\Services\Meter\PropertyReadingService;
use App\Services\Object\ObjectPropertyFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeterController extends Controller
{
    public function __construct(
        private readonly PropertyReadingService $propertyReadingService,
        private readonly ObjectPropertyFileService $objectPropertyFileService,
    ) {
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:object_properties,id'],
            'value' => ['required', 'numeric'],
        ]);

        $reading = $this->propertyReadingService->record(
            objectPropertyId: (int) $validated['id'],
            value: (float) $validated['value'],
        );

        return response()->json([
            'status' => 'ok',
            'reading_id' => $reading->id,
        ]);
    }

    public function uploadPhoto(Request $request, int $readingId): JsonResponse
    {
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:10240',
            ],
        ]);

        $reading = PropertyReading::query()->findOrFail($readingId);

        $file = $this->objectPropertyFileService->storeForReading(
            $reading,
            $validated['file'],
        );

        return response()->json([
            'status' => 'ok',
            'file_id' => $file->id,
        ]);
    }
}

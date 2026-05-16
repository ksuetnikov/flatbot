<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Meter;

use App\Http\Controllers\Controller;
use App\Services\Meter\PropertyReadingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeterController extends Controller
{
    public function __construct(
        private readonly PropertyReadingService $propertyReadingService,
    ) {
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:object_properties,id'],
            'value' => ['required', 'numeric'],
        ]);

        $this->propertyReadingService->record(
            objectPropertyId: (int) $validated['id'],
            value: (float) $validated['value'],
        );

        return response()->json(['status' => 'ok']);
    }
}

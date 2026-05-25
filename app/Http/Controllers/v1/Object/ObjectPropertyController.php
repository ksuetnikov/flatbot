<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\DTO\Object\ObjectPropertyDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Object\CreateObjectPropertyRequest;
use App\Http\Requests\Object\UpdateObjectPropertyRequest;
use App\Models\Object\ObjectProperty;
use App\Http\Resources\Object\ObjectPropertyGetResource;
use App\Models\Object\ObjectModel;
use App\Services\Object\ObjectPropertyService;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * @author Kirill Suetnikov
 */
class ObjectPropertyController extends Controller
{
    public function __construct(
        private readonly ObjectPropertyService $objectPropertyService,
    ) {
    }

    /** Создание свойства объекта
     *
     * @param CreateObjectPropertyRequest $request
     * @param ObjectModel $object
     *
     * @return ObjectPropertyGetResource
     */
    public function create(
        CreateObjectPropertyRequest $request,
        ObjectModel $object
    ): ObjectPropertyGetResource {
        $validated = $request->validated();
        \Log::info('Создается свойство для объекта с айди:');
        \Log::info($object->getKey());
        $dto = ObjectPropertyDTO::from($validated);
        $subscribeUser = $this
            ->objectPropertyService
            ->create(
                objectId: $object->getKey(),
                dto: $dto
            );

        return ObjectPropertyGetResource::make($subscribeUser);
    }

    /** История показаний счётчика */
    public function show(ObjectModel $object, int $propertyId): InertiaResponse
    {
        $property = ObjectProperty::query()
            ->where('object_id', $object->getKey())
            ->with(['readings', 'files'])
            ->findOrFail($propertyId);

        return Inertia::render('object/property-readings', [
            'object' => [
                'id' => $object->id,
                'name' => $object->name,
            ],
            'property' => [
                'id' => $property->id,
                'name' => $property->name,
                'code' => $property->code,
                'description' => $property->description,
                'readings' => $property->readings->map(static fn ($reading) => [
                    'id' => $reading->id,
                    'value' => $reading->value,
                    'recorded_at' => $reading->recorded_at,
                ]),
                'files' => $property->files->map(fn ($file) => [
                    'id' => $file->id,
                    'original_name' => $file->original_name,
                    'size' => $file->size,
                    'mime_type' => $file->mime_type,
                    'url' => route('property.files.show', [$object->id, $property->id, $file->id]),
                ]),
            ],
        ]);
    }

    public function update(
        UpdateObjectPropertyRequest $request,
        ObjectModel $object,
        int $propertyId,
    ): ObjectPropertyGetResource {
        $property = ObjectProperty::query()
            ->where('object_id', $object->getKey())
            ->findOrFail($propertyId);

        $dto = ObjectPropertyDTO::from($request->validated());
        $property = $this->objectPropertyService->update($property, $dto);

        return ObjectPropertyGetResource::make($property);
    }

    /** Удаление свойства объекта
     *
     * @param ObjectModel $object
     * @param int $propertyId
     *
     * @return Response
     */
    public function destroy(ObjectModel $object, int $propertyId): Response
    {
        \Log::info('Удаляется свойство для объекта с айди:');
        \Log::info($propertyId);
        $this
            ->objectPropertyService
            ->delete($propertyId);

        return response()->noContent();
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\DTO\Object\ObjectPropertyDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\Object\ObjectPropertyGetResource;
use App\Models\Object\ObjectModel;
use App\Services\Object\ObjectPropertyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @author Kirill Suetnikov
 */
class ObjectPropertyController extends Controller
{
    public function __construct(
        private readonly ObjectPropertyService $objectPropertyService,
    ) {}

    /**
     * @param Request $request
     * @param ObjectModel $object
     *
     * @return ObjectPropertyGetResource
     */
    public function create(Request $request, ObjectModel $object): ObjectPropertyGetResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'sometimes|string|max:255',
        ]);
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

    /** Удаление подписчика из объекта
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

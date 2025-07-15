<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\DTO\ObjectProperty\ObjectPropertyDTO;
use App\DTO\SubscribeUser\SubscribeUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\Object\ObjectPropertyGetResource;
use App\Http\Resources\SubscribeUser\SubscribeUserGetResource;
use App\Models\Object\ObjectModel;
use App\Models\Object\ObjectProperty;
use App\Services\Object\ObjectPropertyService;
use App\Services\SubscribeUser\SubscribeUserService;
use Illuminate\Http\RedirectResponse;
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
        // $object->update($validated);

        return ObjectPropertyGetResource::make($subscribeUser);
    }

    /** Удаление подписчика из объекта
     *
     * @param ObjectModel $object
     * @param int $subscriberId
     *
     * @return Response
     */
    public function destroy(ObjectModel $object, int $subscriberId): Response
    {
        $this
            ->objectPropertyService
            ->delete($subscriberId);

        return response()->noContent();
    }
}

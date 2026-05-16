<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\SubscribeUser;

use App\DTO\SubscribeUser\SubscribeUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeUser\UpdateSubscribeUserRequest;
use App\Http\Resources\SubscribeUser\SubscribeUserGetResource;
use App\Models\Object\ObjectModel;
use App\Models\SubscribeUser\SubscribeUser;
use App\Services\SubscribeUser\SubscribeUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @author Kirill Suetnikov
 */
class SubscribeUserController extends Controller
{
    public function __construct(
        private readonly SubscribeUserService $subscribeUserService
    ) {
    }

    /**
     * @param Request $request
     * @param ObjectModel $object
     *
     * @return RedirectResponse
     */
    public function create(Request $request, ObjectModel $object)
    {
        // $this->authorize('update', $object);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $dto = SubscribeUserDTO::from($validated);
        $subscribeUser = $this
            ->subscribeUserService
            ->create(
                objectId: $object->getKey(),
                dto: $dto
            );

        return SubscribeUserGetResource::make($subscribeUser);
    }

    public function update(
        UpdateSubscribeUserRequest $request,
        ObjectModel $object,
        int $subscriberId,
    ): SubscribeUserGetResource {
        $subscriber = SubscribeUser::query()
            ->where('object_id', $object->getKey())
            ->findOrFail($subscriberId);

        $dto = SubscribeUserDTO::from($request->validated());
        $subscriber = $this->subscribeUserService->update($subscriber, $dto);

        return SubscribeUserGetResource::make($subscriber);
    }

    /** Удаление подписчика из объекта
     *
     * @param ObjectModel $object
     * @param int $subscriberId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObjectModel $object, int $subscriberId)
    {
        // $this->authorize('delete', $object);
        \Log::info('destroy subscribers');
        $this->subscribeUserService->delete($subscriberId);

        return response()->noContent();
    }
}

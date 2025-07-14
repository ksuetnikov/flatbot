<?php

declare(strict_types=1);

namespace App\Http\Services\SubscribeUser;

use App\Http\DTO\SubscribeUser\SubscribeUserDTO;
use App\Models\SubscribeUser\SubscribeUser;

/**
 * @author Kirill Suetnikov
 */
class SubscribeUserService
{
    public function create(int $objectId, SubscribeUserDTO $dto): SubscribeUser
    {
        $subscribeUser = new SubscribeUser();
        $subscribeUser->name = $dto->name;
        $subscribeUser->object_id = $objectId;
        $subscribeUser->order = 1;
        $subscribeUser->save();

        return $subscribeUser;
    }
}

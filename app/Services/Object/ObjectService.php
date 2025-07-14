<?php

declare(strict_types=1);

namespace App\Services\Object;

use App\DTO\SubscribeUser\SubscribeUserDTO;
use App\Models\Object\ObjectModel;
use App\Models\SubscribeUser\SubscribeUser;
use App\Models\User;

/**
 * @author Kirill Suetnikov
 */
class ObjectService
{
    public function create(User $user): ObjectModel
    {
        $object = new ObjectModel();
        $object->name = ObjectModel::DEFAULT_NAME;
        $object->user_id = $user->getKey();
        $object->order = 1;
        $object->save();

        return $object;
    }
}

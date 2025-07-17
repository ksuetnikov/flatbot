<?php

declare(strict_types=1);

namespace App\Services\Object;

use App\DTO\ObjectProperty\ObjectPropertyDTO;
use App\DTO\SubscribeUser\SubscribeUserDTO;
use App\Models\Object\ObjectProperty;
use App\Models\SubscribeUser\SubscribeUser;

/**
 * @author Kirill Suetnikov
 */
class ObjectPropertyService
{
    public function create(int $objectId, ObjectPropertyDTO $dto): ObjectProperty
    {
        $objectProperty = new ObjectProperty();
        $objectProperty->name = $dto->name;
        $objectProperty->code = $dto->code;
        $objectProperty->object_id = $objectId;
        $objectProperty->order = 1;
        $objectProperty->save();

        return $objectProperty;
    }

    /**
     * @param int $subscribeUserId
     *
     * @return void
     */
    public function delete(int $subscribeUserId): void
    {
         ObjectProperty::destroy($subscribeUserId);
    }
}

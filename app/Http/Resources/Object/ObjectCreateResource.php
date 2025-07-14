<?php

namespace App\Http\Resources\Object;

use App\Http\Resources\BaseResource;
use App\Models\Object\ObjectModel;
use App\Models\SubscribeUser\SubscribeUser;
use Illuminate\Http\Request;

/**
 * @author Kirill Suetnikov
 */
class ObjectCreateResource extends BaseResource
{
    /**
     * @param Request $request
     *
     * @return array<string, null|int|string>
     */
    public function toArray(Request $request): array
    {
        /** @var ObjectModel $this */
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'order' => $this->order,
            'createdAt' => $this->created_at,
        ];
    }
}

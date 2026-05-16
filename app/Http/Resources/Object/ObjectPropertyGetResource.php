<?php

namespace App\Http\Resources\Object;

use App\Http\Resources\BaseResource;
use App\Models\Object\ObjectProperty;
use Illuminate\Http\Request;

/**
 * @author Kirill Suetnikov
 */
class ObjectPropertyGetResource extends BaseResource
{
    /**
     * @param Request $request
     *
     * @return array<string, null|int|string>
     */
    public function toArray(Request $request): array
    {
        /** @var ObjectProperty $this */
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'code' => $this->code,
            'value' => $this->value,
            'order' => $this->order,
            'createdAt' => $this->created_at,
        ];
    }
}

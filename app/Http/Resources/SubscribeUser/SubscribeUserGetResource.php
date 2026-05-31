<?php

namespace App\Http\Resources\SubscribeUser;

use App\Http\Resources\BaseResource;
use App\Models\SubscribeUser\SubscribeUser;
use Illuminate\Http\Request;

/**
 * @author Kirill Suetnikov
 */
class SubscribeUserGetResource extends BaseResource
{
    /**
     * @param Request $request
     *
     * @return array<string, null|bool|int|string>
     */
    public function toArray(Request $request): array
    {
        /** @var SubscribeUser $this */
        $telegramUser = $this->relationLoaded('telegramUser') ? $this->telegramUser : null;

        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'order' => $this->order,
            'registrationKey' => $telegramUser?->registration_key,
            'isRegistered' => $telegramUser?->isRegistered() ?? false,
            'telegramUsername' => $telegramUser?->username,
            'createdAt' => $this->created_at,
        ];
    }
}

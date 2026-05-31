<?php

namespace App\Http\Requests\SubscribeUser;

use App\Http\Requests\BaseRequest;
use App\Models\SubscribeUser\SubscribeUser;
use Illuminate\Validation\Rule;

class UpdateSubscribeUserRequest extends BaseRequest
{
    /**
     * @return array<string, array<int, mixed|string>>
     */
    public function rules(): array
    {
        /** @var null|SubscribeUser $subscriber */
        $subscriber = SubscribeUser::query()->find($this->route('subscriberId'));

        return [
            'name' => ['required', 'string', 'max:255'],
            'registration_key' => [
                'sometimes',
                'nullable',
                'string',
                'min:4',
                'max:32',
                Rule::unique('telegram_users', 'registration_key')->ignore($subscriber?->telegram_user_id),
            ],
            'regenerate_key' => ['sometimes', 'boolean'],
        ];
    }
}

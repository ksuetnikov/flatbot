<?php

namespace App\Http\Requests\SubscribeUser;

use App\Http\Requests\BaseRequest;

class UpdateSubscribeUserRequest extends BaseRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}

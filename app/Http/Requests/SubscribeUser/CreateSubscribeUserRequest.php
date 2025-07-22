<?php

namespace App\Http\Requests\SubscribeUser;

use App\Http\Requests\BaseRequest;

class CreateSubscribeUserRequest extends BaseRequest
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}

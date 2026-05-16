<?php

namespace App\Http\Requests\Object;

use App\Http\Requests\BaseRequest;

class UpdateObjectPropertyRequest extends BaseRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
        ];
    }
}

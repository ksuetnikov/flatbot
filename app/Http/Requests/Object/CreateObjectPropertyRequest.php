<?php

namespace App\Http\Requests\Object;

use App\Http\Requests\BaseRequest;

class CreateObjectPropertyRequest extends BaseRequest
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
        ];
    }
}

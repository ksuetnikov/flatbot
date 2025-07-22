<?php

namespace App\Http\Requests\Object;

use App\Http\Requests\BaseRequest;

class UpdateObjectRequest extends BaseRequest
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:255'],
        ];
    }
}

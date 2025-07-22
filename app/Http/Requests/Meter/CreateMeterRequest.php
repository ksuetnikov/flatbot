<?php

namespace App\Http\Requests\Meter;

use App\Http\Requests\BaseRequest;

class CreateMeterRequest extends BaseRequest
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

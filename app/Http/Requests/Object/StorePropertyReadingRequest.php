<?php

namespace App\Http\Requests\Object;

use App\Http\Requests\BaseRequest;

class StorePropertyReadingRequest extends BaseRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', 'numeric'],
            'recorded_at' => ['required', 'date'],
        ];
    }
}

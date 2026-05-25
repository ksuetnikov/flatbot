<?php

namespace App\Http\Requests\Object;

use App\Http\Requests\BaseRequest;

class UpdateObjectRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('notification_date') === '') {
            $this->merge(['notification_date' => null]);
        }
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notification_date' => ['nullable', 'date'],
        ];
    }
}

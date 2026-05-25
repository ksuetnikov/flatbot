<?php

declare(strict_types=1);

namespace App\Http\Requests\Object;

use App\Http\Requests\BaseRequest;

class UpdateObjectPropertyFileRequest extends BaseRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'original_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^\\\\\\/]+$/',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'original_name.required' => 'Укажите имя файла.',
            'original_name.max' => 'Имя файла не длиннее 255 символов.',
            'original_name.regex' => 'Имя не должно содержать символы / и \\.',
        ];
    }
}

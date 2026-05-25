<?php

declare(strict_types=1);

namespace App\Http\Requests\Object;

use App\Http\Requests\BaseRequest;

class StoreObjectPropertyFileRequest extends BaseRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            /** Один файл до 10 МБ: изображения или PDF (правило max задаётся в килобайтах) */
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,pdf',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Файл не был получен сервером. Частая причина — лимиты PHP: задайте upload_max_filesize и post_max_size не меньше 12M (см. composer script dev).',
            'file.max' => 'Файл слишком большой (максимум 10 МБ).',
            'file.mimes' => 'Разрешены только изображения (jpg, png, gif, webp) и PDF.',
        ];
    }
}

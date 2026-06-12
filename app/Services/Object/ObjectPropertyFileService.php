<?php

declare(strict_types=1);

namespace App\Services\Object;

use App\Models\Object\ObjectPropertyFile;
use App\Models\Object\PropertyReading;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ObjectPropertyFileService
{
    public function storeForReading(PropertyReading $reading, UploadedFile $file): ObjectPropertyFile
    {
        ObjectPropertyFile::query()
            ->where('property_reading_id', $reading->getKey())
            ->each(function (ObjectPropertyFile $existing): void {
                $existing->delete();
            });

        $path = $file->store("property-files/{$reading->object_property_id}", 'public');
        if ($path === false || ! Storage::disk('public')->exists($path)) {
            throw new \RuntimeException('Не удалось сохранить файл в storage.');
        }

        return ObjectPropertyFile::query()->create([
            'object_property_id' => $reading->object_property_id,
            'property_reading_id' => $reading->getKey(),
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}

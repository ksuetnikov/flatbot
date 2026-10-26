<?php

declare(strict_types=1);

namespace App\Services\Object;

use App\Models\Object\ObjectProperty;
use App\Models\Object\ObjectPropertyFile;
use App\Models\Object\PropertyReading;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Единая запись вложения счётчика на диск public и в object_property_files.
 * Фото показания перед записью заменяет предыдущие файлы этого показания.
 * Файл без показания просто добавляется к счётчику.
 */
class ObjectPropertyFileService
{
    public function store(ObjectProperty $property, UploadedFile $file): ObjectPropertyFile
    {
        return $this->persist($property->id, $file, null);
    }

    public function storeForReading(PropertyReading $reading, UploadedFile $file): ObjectPropertyFile
    {
        $this->deleteFilesForReading($reading);

        return $this->persist($reading->object_property_id, $file, $reading->id);
    }

    private function deleteFilesForReading(PropertyReading $reading): void
    {
        ObjectPropertyFile::query()
            ->where('property_reading_id', $reading->id)
            ->each(static function (ObjectPropertyFile $existing): void {
                $existing->delete();
            });
    }

    private function persist(int $objectPropertyId, UploadedFile $file, ?int $propertyReadingId): ObjectPropertyFile
    {
        $path = $file->store('property-files/'.$objectPropertyId, 'public');
        if ($path === false || ! Storage::disk('public')->exists($path)) {
            throw new RuntimeException('Не удалось сохранить файл в storage.');
        }

        $size = $file->getSize();
        if (! is_int($size)) {
            throw new RuntimeException('Не удалось сохранить файл в storage.');
        }

        return ObjectPropertyFile::query()->create([
            'object_property_id' => $objectPropertyId,
            'property_reading_id' => $propertyReadingId,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $size,
        ]);
    }
}

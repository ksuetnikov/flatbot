<?php

declare(strict_types=1);

namespace App\Models\Object;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Файл, прикреплённый к свойству объекта (счётчику).
 *
 * @property int $id
 * @property int $object_property_id
 * @property null|int $property_reading_id
 * @property-read null|PropertyReading $propertyReading
 * @property string $disk
 * @property string $path
 * @property string $original_name
 * @property null|string $mime_type
 * @property int $size
 *
 * @property-read ObjectProperty $objectProperty
 *
 * @mixin Eloquent
 */
class ObjectPropertyFile extends Model
{
    protected $table = 'object_property_files';

    protected $fillable = [
        'object_property_id',
        'property_reading_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $file): void {
            if ($file->path !== '') {
                Storage::disk($file->disk)->delete($file->path);
            }
        });
    }

    public function objectProperty(): BelongsTo
    {
        return $this->belongsTo(ObjectProperty::class, 'object_property_id', 'id');
    }

    public function propertyReading(): BelongsTo
    {
        return $this->belongsTo(PropertyReading::class, 'property_reading_id', 'id');
    }

    /** Публичный URL через симлинк storage (если выполнен `php artisan storage:link`). */
    public function publicStorageUrl(): string
    {
        return '/storage/'.ltrim($this->path, '/');
    }

    public function existsOnDisk(): bool
    {
        return $this->path !== '' && Storage::disk($this->disk)->exists($this->path);
    }
}

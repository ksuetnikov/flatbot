<?php

declare(strict_types=1);

namespace App\Models\Object;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Файл, прикреплённый к свойству объекта (счётчику).
 *
 * @property int $id Идентификатор файла
 * @property int $object_property_id Идентификатор счётчика, object_properties.id
 * @property int|null $property_reading_id Показание, если это его фото. Пусто у вложения счётчика
 * @property string $disk Имя диска Laravel, для новых файлов public
 * @property string $path Путь на диске
 * @property string $original_name Имя файла, которое видит пользователь
 * @property string|null $mime_type MIME-тип
 * @property int $size Размер в байтах
 * @property Carbon|null $created_at Дата загрузки
 * @property Carbon|null $updated_at Дата изменения
 * @property-read ObjectProperty $objectProperty Счётчик
 * @property-read PropertyReading|null $propertyReading Показание, к которому привязано фото
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

    /**
     * Счётчик, к которому прикреплён файл.
     *
     * @return BelongsTo<ObjectProperty, $this>
     */
    public function objectProperty(): BelongsTo
    {
        return $this->belongsTo(ObjectProperty::class, 'object_property_id', 'id');
    }

    /**
     * Показание, если файл — его фото.
     *
     * @return BelongsTo<PropertyReading, $this>
     */
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

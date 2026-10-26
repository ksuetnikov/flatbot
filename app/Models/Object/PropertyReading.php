<?php

declare(strict_types=1);

namespace App\Models\Object;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Одна передача показания счётчика.
 *
 * @property int $id Идентификатор показания
 * @property int $object_property_id Идентификатор счётчика, object_properties.id
 * @property string $value Показание, decimal(20, 4)
 * @property Carbon $recorded_at Дата и время снятия
 * @property Carbon|null $created_at Дата создания строки
 * @property Carbon|null $updated_at Дата обновления строки
 * @property-read ObjectProperty $objectProperty Счётчик
 * @property-read ObjectPropertyFile|null $photo Фото этого показания, не больше одного
 */
class PropertyReading extends Model
{
    public $table = 'property_readings';

    protected $fillable = [
        'object_property_id',
        'value',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'recorded_at' => 'datetime',
        ];
    }

    /**
     * Счётчик, к которому относится показание.
     *
     * @return BelongsTo<ObjectProperty, $this>
     */
    public function objectProperty(): BelongsTo
    {
        return $this->belongsTo(ObjectProperty::class, 'object_property_id', 'id');
    }

    /**
     * Фото показания. Уникальный индекс не даёт хранить больше одного файла.
     *
     * @return HasOne<ObjectPropertyFile, $this>
     */
    public function photo(): HasOne
    {
        return $this->hasOne(ObjectPropertyFile::class, 'property_reading_id', 'id');
    }
}

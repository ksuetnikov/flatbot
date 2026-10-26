<?php

declare(strict_types=1);

namespace App\Models\Object;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @author Kirill Suetnikov
 *
 * Модель свойств объектов - создаваемые пользователем кастомные сущности
 *
 * @property int $id Идентификатор счётчика
 * @property string $order Индекс сортировки, decimal(30, 10). Без cast Eloquent возвращает строку
 * @property int|null $object_id Идентификатор объекта, objects.id
 * @property string $name Название счётчика
 * @property string|null $code Код счётчика
 * @property string|null $value Последнее показание. Пересчитывает PropertyReadingService
 * @property string|null $description Описание
 * @property Carbon|null $created_at Дата создания
 * @property Carbon|null $updated_at Дата обновления
 * @property Carbon|null $deleted_at Колонка есть, мягкое удаление не включено
 * @property-read ObjectModel $object Объект, к которому относится счётчик
 * @property-read Collection<int, PropertyReading> $readings История показаний, новые сверху
 * @property-read PropertyReading|null $latestReading Показание с максимальным recorded_at
 * @property-read Collection<int, ObjectPropertyFile> $files Вложения счётчика, новые сверху
 *
 * @method static Builder<self>|self query()
 *
 * @mixin Eloquent
 */
class ObjectProperty extends Model
{
    /** @var string */
    public $table = 'object_properties';

    protected $fillable = [
        'name',
        'code',
        'description',
        'value',
    ];

    /**
     * Объект, к которому относится счётчик.
     *
     * @return BelongsTo<ObjectModel, $this>
     */
    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class, 'object_id', 'id');
    }

    /**
     * История показаний, от новых к старым.
     *
     * @return HasMany<PropertyReading, $this>
     */
    public function readings(): HasMany
    {
        return $this->hasMany(PropertyReading::class, 'object_property_id', 'id')
            ->orderByDesc('recorded_at');
    }

    /**
     * Показание с самой поздней датой снятия.
     *
     * @return HasOne<PropertyReading, $this>
     */
    public function latestReading(): HasOne
    {
        return $this->hasOne(PropertyReading::class, 'object_property_id', 'id')
            ->latestOfMany('recorded_at');
    }

    /**
     * Файлы счётчика, включая фото показаний.
     *
     * @return HasMany<ObjectPropertyFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(ObjectPropertyFile::class, 'object_property_id', 'id')
            ->orderByDesc('created_at');
    }
}

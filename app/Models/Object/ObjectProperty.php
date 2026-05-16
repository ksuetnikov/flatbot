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
 * @property int $id Идентификатор
 * @property float $order Индекс сортировки
 * @property int $object_id Идентификатор свойства
 * @property string $name Наименование свойства
 * @property null|string $code Код свойства
 * @property null|string $value Значение свойства
 * @property null|string $description Описание свойства
 * @property null|Carbon $created_at Дата создания
 * @property null|Carbon $updated_at Дата обновления
 * @property null|Carbon $deleted_at Дата удаления
 *
 * @method static Builder|self query()
 *
 * @property-read ObjectModel $object Объект
 * @property-read Collection<PropertyReading> $readings История показаний
 * @property-read null|PropertyReading $latestReading Последнее показание
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
     * Объект, к которому относится свойство
     */
    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class, 'object_id', 'id');
    }

    public function readings(): HasMany
    {
        return $this->hasMany(PropertyReading::class, 'object_property_id', 'id')
            ->orderByDesc('recorded_at');
    }

    public function latestReading(): HasOne
    {
        return $this->hasOne(PropertyReading::class, 'object_property_id', 'id')
            ->latestOfMany('recorded_at');
    }
}

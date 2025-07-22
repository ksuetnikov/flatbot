<?php

declare(strict_types=1);

namespace App\Models\Object;

use App\Models\Object\ObjectModel;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 *
 * @mixin Eloquent
 */
class ObjectProperty extends Model
{
    /** @var string */
    public $table = 'object_properties';

    protected $fillable =[
        'name'
    ];

    /**
     * Объект, к которому относится свойство
     */
    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class,'object_id', 'id');
    }
}

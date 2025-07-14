<?php

declare(strict_types=1);

namespace App\Models\SubscribeUser;

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
 * Модель объектов - создаваемые пользователем сущности
 *
 * @property int $id Идентификатор
 * @property float $order Индекс сортировки
 * @property int $object_id Идентификатор объекта
 * @property string $name Наименование объекта
 * @property null|string $description Описание объекта
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
class SubscribeUser extends Model
{
    /** @var string */
    public $table = 'subscribe_users';

    protected $fillable =[
        'name'
    ];

    /**
     * Объект, на который подписан подписчик
     */
    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class,'object_id', 'id');
    }
}

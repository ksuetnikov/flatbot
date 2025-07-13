<?php

declare(strict_types=1);

namespace App\Models\Object;

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
 * @property int $user_id Идентификатор пользователя
 * @property string $name Наименование объекта
 * @property null|string $description Описание объекта
 * @property null|Carbon $created_at Дата создания
 * @property null|Carbon $updated_at Дата обновления
 * @property null|Carbon $deleted_at Дата удаления
 *
 * @method static Builder|self query()
 *
 * @mixin Eloquent
 */
class ObjectModel extends Model
{
    /** @var string */
    public $table = 'objects';

    /**
     * Пользователь, создавший данный объект
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id', 'id');
    }
}

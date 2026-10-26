<?php

declare(strict_types=1);

namespace App\Models\Object;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id Идентификатор записи
 * @property int $object_id Идентификатор объекта, objects.id
 * @property int $year Год
 * @property int $month Месяц, 1–12
 * @property int $day День месяца, 1–31
 * @property string $type Вид записи: ObjectFinanceEntry::TYPE_INCOME или TYPE_EXPENSE
 * @property string $amount Сумма, decimal(14, 2)
 * @property string|null $comment Комментарий, до 500 символов
 * @property Carbon|null $created_at Дата создания
 * @property Carbon|null $updated_at Дата обновления
 * @property-read ObjectModel $object Объект
 *
 * @method static Builder|self query()
 *
 * @mixin Eloquent
 */
class ObjectFinanceEntry extends Model
{
    public const TYPE_INCOME = 'income';

    public const TYPE_EXPENSE = 'expense';

    protected $table = 'object_finance_entries';

    protected $fillable = [
        'object_id',
        'year',
        'month',
        'day',
        'type',
        'amount',
        'comment',
    ];

    /**
     * Объект, к которому относится запись.
     *
     * @return BelongsTo<ObjectModel, $this>
     */
    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class, 'object_id', 'id');
    }
}

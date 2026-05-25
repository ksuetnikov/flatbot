<?php

declare(strict_types=1);

namespace App\Models\Object;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $object_id
 * @property int $year
 * @property int $month
 * @property int $day
 * @property string $type income|expense
 * @property string $amount
 * @property null|string $comment
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property-read ObjectModel $object
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

    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class, 'object_id', 'id');
    }
}

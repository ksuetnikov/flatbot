<?php

declare(strict_types=1);

namespace App\Models\Object;

use App\Models\SubscribeUser\SubscribeUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $object_id
 * @property int $subscribe_user_id
 * @property int $period_year
 * @property int $period_month
 * @property string $reminder_type
 * @property int $schedule_day
 * @property int $schedule_hour
 * @property int $schedule_minute
 * @property Carbon $sent_at
 */
class ReadingReminderLog extends Model
{
    public const TYPE_DUE = 'due';

    public const TYPE_OVERDUE = 'overdue';

    protected $table = 'reading_reminder_logs';

    protected $fillable = [
        'object_id',
        'subscribe_user_id',
        'period_year',
        'period_month',
        'reminder_type',
        'schedule_day',
        'schedule_hour',
        'schedule_minute',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'period_year' => 'integer',
            'period_month' => 'integer',
            'schedule_day' => 'integer',
            'schedule_hour' => 'integer',
            'schedule_minute' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class, 'object_id', 'id');
    }

    public function subscribeUser(): BelongsTo
    {
        return $this->belongsTo(SubscribeUser::class, 'subscribe_user_id', 'id');
    }
}

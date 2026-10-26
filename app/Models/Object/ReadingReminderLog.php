<?php

declare(strict_types=1);

namespace App\Models\Object;

use App\Models\SubscribeUser\SubscribeUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Отправленное Telegram-напоминание о показаниях.
 * Строки пишет бот. Панель удаляет их, когда меняется расписание объекта.
 *
 * @property int $id Идентификатор записи журнала
 * @property int $object_id Идентификатор объекта, objects.id
 * @property int $subscribe_user_id Идентификатор подписчика, subscribe_users.id
 * @property int $period_year Год периода сдачи
 * @property int $period_month Месяц периода сдачи, 1–12
 * @property string $reminder_type Тип: ReadingReminderLog::TYPE_DUE или TYPE_OVERDUE
 * @property int $schedule_day День расписания, по которому отправлено напоминание
 * @property int $schedule_hour Час расписания
 * @property int $schedule_minute Минута расписания
 * @property Carbon $sent_at Когда напоминание отправлено
 * @property Carbon|null $created_at Дата создания строки
 * @property Carbon|null $updated_at Дата обновления строки
 * @property-read ObjectModel $object Объект
 * @property-read SubscribeUser $subscribeUser Подписчик
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

    /**
     * Объект, по которому отправлено напоминание.
     *
     * @return BelongsTo<ObjectModel, $this>
     */
    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class, 'object_id', 'id');
    }

    /**
     * Подписчик, которому ушло напоминание.
     *
     * @return BelongsTo<SubscribeUser, $this>
     */
    public function subscribeUser(): BelongsTo
    {
        return $this->belongsTo(SubscribeUser::class, 'subscribe_user_id', 'id');
    }
}

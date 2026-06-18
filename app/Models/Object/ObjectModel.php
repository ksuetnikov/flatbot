<?php

declare(strict_types=1);

namespace App\Models\Object;

use App\Casts\MonthlyNotificationSchedule;
use App\Models\SubscribeUser\SubscribeUser;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property null|string $notification_date Ежемесячное расписание уведомлений (день + время)
 * @property null|Carbon $created_at Дата создания
 * @property null|Carbon $updated_at Дата обновления
 * @property null|Carbon $deleted_at Дата удаления
 *
 * @method static Builder|self query()
 * @property-read Collection<SubscribeUser> $subscribers Подписчики
 * @property-read Collection<ObjectProperty> $properties Свойства
 * @property-read Collection<ObjectFinanceEntry> $financeEntries Финансы
 * @property-read User $user Пользователь
 *
 * @mixin Eloquent
 */
class ObjectModel extends Model
{
    use HasFactory;

    /** @var string */
    public const DEFAULT_NAME = 'Новый объект';

    /** @var string */
    public $table = 'objects';

    protected $fillable = [
        'name',
        'description',
        'notification_date',
    ];

    protected function casts(): array
    {
        return [
            'notification_date' => MonthlyNotificationSchedule::class,
        ];
    }

    protected static function booted(): void
    {
        static::updated(function (self $object): void {
            if ($object->wasChanged('notification_date')) {
                ReadingReminderLog::query()
                    ->where('object_id', $object->id)
                    ->delete();
            }
        });
    }

    /**
     * Пользователь, создавший данный объект
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Подписчики объекта
     */
    public function subscribers(): HasMany
    {
        return $this->hasMany(SubscribeUser::class, 'object_id', 'id');
    }

    /**
     * Свойства объекта
     */
    public function properties(): HasMany
    {
        return $this->hasMany(ObjectProperty::class, 'object_id', 'id');
    }

    /**
     * Записи доходов и расходов
     */
    public function financeEntries(): HasMany
    {
        return $this->hasMany(ObjectFinanceEntry::class, 'object_id', 'id');
    }
}

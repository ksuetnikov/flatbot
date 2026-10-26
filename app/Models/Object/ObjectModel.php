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
 * @property string $order Индекс сортировки, decimal(30, 10). Без cast Eloquent возвращает строку
 * @property int|null $user_id Идентификатор пользователя панели, users.id
 * @property string $name Наименование объекта
 * @property string|null $description Описание объекта
 * @property string|null $notification_date Ежемесячное расписание уведомлений: день и время, каст MonthlyNotificationSchedule
 * @property string|null $user_code Код пользователя в Telegram. Панель это поле не заполняет
 * @property string|null $bot_properties JSON свойств для Telegram-бота. Панель это поле не заполняет
 * @property Carbon|null $created_at Дата создания
 * @property Carbon|null $updated_at Дата обновления
 * @property Carbon|null $deleted_at Колонка есть, мягкое удаление не включено
 * @property-read User $user Пользователь, создавший объект
 * @property-read Collection<int, SubscribeUser> $subscribers Подписчики объекта
 * @property-read Collection<int, ObjectProperty> $properties Счётчики объекта
 * @property-read Collection<int, ObjectFinanceEntry> $financeEntries Доходы и расходы
 *
 * @method static Builder<self>|self query()
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
     * Пользователь панели, которому принадлежит объект.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Подписчики объекта.
     *
     * @return HasMany<SubscribeUser, $this>
     */
    public function subscribers(): HasMany
    {
        return $this->hasMany(SubscribeUser::class, 'object_id', 'id');
    }

    /**
     * Счётчики объекта.
     *
     * @return HasMany<ObjectProperty, $this>
     */
    public function properties(): HasMany
    {
        return $this->hasMany(ObjectProperty::class, 'object_id', 'id');
    }

    /**
     * Доходы и расходы объекта.
     *
     * @return HasMany<ObjectFinanceEntry, $this>
     */
    public function financeEntries(): HasMany
    {
        return $this->hasMany(ObjectFinanceEntry::class, 'object_id', 'id');
    }
}

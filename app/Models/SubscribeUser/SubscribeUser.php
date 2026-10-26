<?php

declare(strict_types=1);

namespace App\Models\SubscribeUser;

use App\Models\Object\ObjectModel;
use App\Models\TelegramUser\TelegramUser;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @author Kirill Suetnikov
 *
 * Подписчики на объекты
 *
 * @property int $id Идентификатор подписчика
 * @property string $order Индекс сортировки, decimal(30, 10). Без cast Eloquent возвращает строку
 * @property int|null $object_id Идентификатор объекта, objects.id
 * @property int|null $telegram_user_id Слот регистрации, telegram_users.id
 * @property string $name Telegram username подписчика без @
 * @property string|null $description Описание. Форма панели его не заполняет
 * @property Carbon|null $created_at Дата создания
 * @property Carbon|null $updated_at Дата обновления
 * @property Carbon|null $deleted_at Колонка есть, мягкое удаление не включено
 *
 * @method static Builder|self query()
 *
 * @property-read ObjectModel $object Объект
 * @property-read null|TelegramUser $telegramUser Слот Telegram-регистрации
 *
 * @mixin Eloquent
 */
class SubscribeUser extends Model
{
    /** @var string */
    public $table = 'subscribe_users';

    protected $fillable = [
        'name',
    ];

    /**
     * Объект, к которому привязан подписчик.
     *
     * @return BelongsTo<ObjectModel, $this>
     */
    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class, 'object_id', 'id');
    }

    /**
     * Слот регистрации в Telegram-боте.
     *
     * @return BelongsTo<TelegramUser, $this>
     */
    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'id');
    }
}

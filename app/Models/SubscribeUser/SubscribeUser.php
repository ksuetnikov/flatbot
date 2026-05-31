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
 * @property int $id Идентификатор
 * @property float $order Индекс сортировки
 * @property int $object_id Идентификатор объекта
 * @property null|int $telegram_user_id Слот регистрации в Telegram
 * @property string $name Telegram username подписчика (без @)
 * @property null|string $description Описание подписчика
 * @property null|Carbon $created_at Дата создания
 * @property null|Carbon $updated_at Дата обновления
 * @property null|Carbon $deleted_at Дата удаления
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
        'name'
    ];

    /**
     * Объект, на который подписан подписчик
     */
    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectModel::class, 'object_id', 'id');
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'id');
    }
}

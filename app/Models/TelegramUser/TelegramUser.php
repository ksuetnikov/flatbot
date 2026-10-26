<?php

declare(strict_types=1);

namespace App\Models\TelegramUser;

use App\Models\SubscribeUser\SubscribeUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Слот регистрации / аккаунт пользователя Telegram-бота.
 *
 * @property int $id Идентификатор слота
 * @property int|null $telegram_id Идентификатор пользователя Telegram. null, пока ключ не активирован
 * @property string|null $first_name Имя из Telegram. Заполняет бот
 * @property string|null $username Username из Telegram. Заполняет бот
 * @property string|null $registration_key Ключ, который панель выдаёт подписчику
 * @property string $state Состояние диалога бота. Панель при создании пишет main_menu
 * @property bool $is_admin Флаг администратора бота. Панель при создании подписчика пишет false
 * @property Carbon|null $created_at Дата создания слота
 * @property-read SubscribeUser|null $subscribeUser Подписчик панели, связанный с этим слотом
 */
class TelegramUser extends Model
{
    public $table = 'telegram_users';

    public $timestamps = false;

    protected $fillable = [
        'telegram_id',
        'first_name',
        'username',
        'registration_key',
        'state',
        'is_admin',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'telegram_id' => 'integer',
            'is_admin' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Ключ уже обменяли на аккаунт Telegram.
     */
    public function isRegistered(): bool
    {
        return $this->telegram_id !== null;
    }

    /**
     * Подписчик панели, которому выдан этот слот.
     *
     * @return HasOne<SubscribeUser, $this>
     */
    public function subscribeUser(): HasOne
    {
        return $this->hasOne(SubscribeUser::class, 'telegram_user_id', 'id');
    }
}

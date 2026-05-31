<?php

declare(strict_types=1);

namespace App\Models\TelegramUser;

use App\Models\SubscribeUser\SubscribeUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Слот регистрации / аккаунт пользователя Telegram-бота.
 *
 * @property int $id
 * @property int|null $telegram_id
 * @property string|null $first_name
 * @property string|null $username
 * @property string|null $registration_key
 * @property string $state
 * @property bool $is_admin
 * @property \Illuminate\Support\Carbon|null $created_at
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

    public function isRegistered(): bool
    {
        return $this->telegram_id !== null;
    }

    public function subscribeUser(): HasOne
    {
        return $this->hasOne(SubscribeUser::class, 'telegram_user_id', 'id');
    }
}

<?php

namespace App\Models;

use App\Models\Object\ObjectModel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Учётная запись веб-панели. Ролей нет.
 *
 * @author Kirill Suetnikov
 *
 * @property int $id Идентификатор пользователя
 * @property string $name Имя
 * @property string $email Почта, уникальная
 * @property Carbon|null $email_verified_at Когда подтверждена почта. Модель не реализует MustVerifyEmail
 * @property string $password Хеш пароля
 * @property string|null $remember_token Токен «запомнить меня»
 * @property Carbon|null $created_at Дата регистрации
 * @property Carbon|null $updated_at Дата обновления профиля
 * @property-read Collection<int, ObjectModel> $objects Объекты этого пользователя
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Объекты, которые создал этот пользователь.
     *
     * @return HasMany<ObjectModel, $this>
     */
    public function objects(): HasMany
    {
        return $this->hasMany(ObjectModel::class, 'user_id', 'id');
    }
}

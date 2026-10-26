<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Подписчик на странице настроек объекта.
 * Имена полей совпадают с JSON, который ждёт resources/js/pages/object/edit.tsx.
 *
 * @implements Arrayable<string, int|string|bool|null>
 */
final readonly class ObjectEditSubscriberData implements Arrayable
{
    /**
     * @param  int  $id  Идентификатор subscribe_users
     * @param  string  $name  Telegram username без @
     * @param  string|null  $registrationKey  Ключ регистрации бота
     * @param  bool  $isRegistered  true, если у слота заполнен telegram_id
     * @param  string|null  $telegramUsername  Username, который записал бот
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $registrationKey,
        public bool $isRegistered,
        public ?string $telegramUsername,
    ) {}

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     registrationKey: string|null,
     *     isRegistered: bool,
     *     telegramUsername: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'registrationKey' => $this->registrationKey,
            'isRegistered' => $this->isRegistered,
            'telegramUsername' => $this->telegramUsername,
        ];
    }
}

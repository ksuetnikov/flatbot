<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use App\Models\Object\ObjectProperty;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;

/**
 * Данные страницы object/edit.
 * allSubscribers и allProperties повторяют списки внутри object — так их ждёт фронтенд.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class ObjectEditData implements Arrayable
{
    /**
     * @param  int  $id  Идентификатор объекта
     * @param  string  $name  Название
     * @param  string|null  $description  Описание
     * @param  string|null  $notificationDate  Расписание напоминаний
     * @param  list<ObjectEditSubscriberData>  $subscribers  Подписчики с ключом регистрации
     * @param  Collection<int, ObjectProperty>  $properties  Счётчики объекта
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public ?string $notificationDate,
        public array $subscribers,
        public Collection $properties,
    ) {}

    /**
     * @return array{
     *     object: array<string, mixed>,
     *     allSubscribers: list<array<string, mixed>>,
     *     allProperties: list<array<string, mixed>>
     * }
     */
    public function toArray(): array
    {
        $subscribers = array_map(
            static fn (ObjectEditSubscriberData $subscriber): array => $subscriber->toArray(),
            $this->subscribers,
        );
        $properties = $this->properties
            ->map(static fn (ObjectProperty $property): array => $property->toArray())
            ->values()
            ->all();

        return [
            'object' => [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'notification_date' => $this->notificationDate,
                'subscribers' => $subscribers,
                'properties' => $properties,
            ],
            'allSubscribers' => $subscribers,
            'allProperties' => $properties,
        ];
    }
}

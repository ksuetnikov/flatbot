<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use App\Models\SubscribeUser\SubscribeUser;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Данные страницы object/show.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class ObjectShowData implements Arrayable
{
    /**
     * @param  int  $id  Идентификатор объекта
     * @param  string  $name  Название
     * @param  string|null  $description  Описание
     * @param  string|null  $notificationDate  Расписание Telegram-напоминаний
     * @param  Carbon|null  $createdAt  Дата создания
     * @param  Collection<int, SubscribeUser>  $subscribers  Подписчики объекта
     * @param  FinanceSummaryData  $financeSummary  Сводка финансов
     * @param  list<ObjectShowPropertyData>  $properties  Счётчики с последним показанием
     * @param array{
     *     period_label: string,
     *     is_complete: bool,
     *     submitted_count: int,
     *     total_count: int,
     *     missing_properties: list<string>,
     *     properties: list<array{
     *         id: int,
     *         name: string,
     *         submitted_this_month: bool,
     *         latest_in_period: array{value: string, recorded_at: Carbon}|null
     *     }>,
     *     subscribers: list<array{id: int, name: string, is_registered: bool}>
     * } $readingStatus Сдача показаний за текущий месяц
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public ?string $notificationDate,
        public ?Carbon $createdAt,
        public Collection $subscribers,
        public FinanceSummaryData $financeSummary,
        public array $properties,
        public array $readingStatus,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'notification_date' => $this->notificationDate,
            'created_at' => $this->createdAt,
            'subscribers' => $this->subscribers
                ->map(static fn (SubscribeUser $subscriber): array => $subscriber->toArray())
                ->values()
                ->all(),
            'finance_summary' => $this->financeSummary->toArray(),
            'properties' => array_map(
                static fn (ObjectShowPropertyData $property): array => $property->toArray(),
                $this->properties,
            ),
            'reading_status' => $this->readingStatus,
        ];
    }
}

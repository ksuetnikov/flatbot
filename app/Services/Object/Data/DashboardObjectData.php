<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

/**
 * Карточка объекта на дашборде.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class DashboardObjectData implements Arrayable
{
    /**
     * @param  int  $id  Идентификатор объекта
     * @param  string  $name  Название
     * @param  string|null  $description  Описание
     * @param  string|null  $notificationDate  Расписание напоминаний, «настенные» дата и время
     * @param  Carbon|null  $createdAt  Дата создания
     * @param  Carbon|null  $updatedAt  Дата обновления
     * @param  FinanceSummaryData  $financeSummary  Сводка финансов за всё время
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public ?string $notificationDate,
        public ?Carbon $createdAt,
        public ?Carbon $updatedAt,
        public FinanceSummaryData $financeSummary,
    ) {}

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     description: string|null,
     *     notification_date: string|null,
     *     created_at: Carbon|null,
     *     updated_at: Carbon|null,
     *     finance_summary: array{total_income: float, total_expense: float, balance: float, entries_count: int}
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'notification_date' => $this->notificationDate,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'finance_summary' => $this->financeSummary->toArray(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Одна строка дохода или расхода внутри месяца.
 *
 * @implements Arrayable<string, int|string|null>
 */
final readonly class FinanceMonthEntryData implements Arrayable
{
    /**
     * @param  int  $id  Идентификатор записи object_finance_entries
     * @param  int  $day  День месяца, 1–31
     * @param  string  $amount  Сумма как decimal-строка
     * @param  string|null  $comment  Комментарий
     */
    public function __construct(
        public int $id,
        public int $day,
        public string $amount,
        public ?string $comment,
    ) {}

    /**
     * @return array{id: int, day: int, amount: string, comment: string|null}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'amount' => $this->amount,
            'comment' => $this->comment,
        ];
    }
}

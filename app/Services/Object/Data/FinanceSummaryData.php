<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Сводка доходов и расходов одного объекта.
 *
 * @implements Arrayable<string, float|int>
 */
final readonly class FinanceSummaryData implements Arrayable
{
    /**
     * @param  float  $totalIncome  Сумма доходов
     * @param  float  $totalExpense  Сумма расходов
     * @param  float  $balance  Доходы минус расходы
     * @param  int  $entriesCount  Число финансовых записей
     */
    public function __construct(
        public float $totalIncome,
        public float $totalExpense,
        public float $balance,
        public int $entriesCount,
    ) {}

    /**
     * @param  array{total_income: float, total_expense: float, balance: float, entries_count: int}  $summary
     */
    public static function fromSummary(array $summary): self
    {
        return new self(
            totalIncome: $summary['total_income'],
            totalExpense: $summary['total_expense'],
            balance: $summary['balance'],
            entriesCount: $summary['entries_count'],
        );
    }

    public static function empty(): self
    {
        return new self(0.0, 0.0, 0.0, 0);
    }

    /**
     * @return array{total_income: float, total_expense: float, balance: float, entries_count: int}
     */
    public function toArray(): array
    {
        return [
            'total_income' => $this->totalIncome,
            'total_expense' => $this->totalExpense,
            'balance' => $this->balance,
            'entries_count' => $this->entriesCount,
        ];
    }
}

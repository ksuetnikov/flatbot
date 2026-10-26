<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Доходы и расходы объекта за один календарный месяц.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class FinanceMonthData implements Arrayable
{
    /**
     * @param  int  $year  Год
     * @param  int  $month  Месяц, 1–12
     * @param  list<FinanceMonthEntryData>  $incomes  Доходы месяца
     * @param  list<FinanceMonthEntryData>  $expenses  Расходы месяца
     * @param  float  $totalIncome  Сумма доходов месяца
     * @param  float  $totalExpense  Сумма расходов месяца
     * @param  float  $balance  Доходы минус расходы за месяц
     */
    public function __construct(
        public int $year,
        public int $month,
        public array $incomes,
        public array $expenses,
        public float $totalIncome,
        public float $totalExpense,
        public float $balance,
    ) {}

    /**
     * @return array{
     *     year: int,
     *     month: int,
     *     incomes: list<array{id: int, day: int, amount: string, comment: string|null}>,
     *     expenses: list<array{id: int, day: int, amount: string, comment: string|null}>,
     *     total_income: float,
     *     total_expense: float,
     *     balance: float
     * }
     */
    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'incomes' => array_map(
                static fn (FinanceMonthEntryData $entry): array => $entry->toArray(),
                $this->incomes,
            ),
            'expenses' => array_map(
                static fn (FinanceMonthEntryData $entry): array => $entry->toArray(),
                $this->expenses,
            ),
            'total_income' => $this->totalIncome,
            'total_expense' => $this->totalExpense,
            'balance' => $this->balance,
        ];
    }
}

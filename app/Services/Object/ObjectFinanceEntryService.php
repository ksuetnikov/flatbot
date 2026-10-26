<?php

declare(strict_types=1);

namespace App\Services\Object;

use App\Models\Object\ObjectFinanceEntry;
use App\Models\Object\ObjectModel;
use App\Services\Object\Data\FinanceMonthData;
use App\Services\Object\Data\FinanceMonthEntryData;

/**
 * Создание, удаление и помесячная сборка финансовых записей объекта.
 */
class ObjectFinanceEntryService
{
    /**
     * @param  string|int|float  $amount  Сумма, как её прислала форма
     */
    public function create(
        ObjectModel $object,
        int $year,
        int $month,
        int $day,
        string $type,
        string|int|float $amount,
        ?string $comment,
    ): ObjectFinanceEntry {
        return $object->financeEntries()->create([
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'type' => $type,
            'amount' => $amount,
            'comment' => $comment,
        ]);
    }

    public function delete(ObjectModel $object, int $entryId): void
    {
        $entry = ObjectFinanceEntry::query()
            ->where('object_id', $object->id)
            ->findOrFail($entryId);

        $entry->delete();
    }

    /**
     * Записи объекта, сгруппированные по году и месяцу.
     * Внутри месяца доходы и расходы идут в том порядке, в котором их вернула выборка.
     *
     * @return list<FinanceMonthData>
     */
    public function months(ObjectModel $object): array
    {
        $entries = $object->financeEntries()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('day')
            ->orderBy('type')
            ->orderBy('id')
            ->get();

        /** @var array<string, array{year: int, month: int, incomes: list<FinanceMonthEntryData>, expenses: list<FinanceMonthEntryData>}> $grouped */
        $grouped = [];

        foreach ($entries as $entry) {
            $key = $entry->year.'-'.$entry->month;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'year' => (int) $entry->year,
                    'month' => (int) $entry->month,
                    'incomes' => [],
                    'expenses' => [],
                ];
            }

            $item = new FinanceMonthEntryData(
                id: $entry->id,
                day: (int) $entry->day,
                amount: (string) $entry->amount,
                comment: $entry->comment,
            );

            if ($entry->type === ObjectFinanceEntry::TYPE_INCOME) {
                $grouped[$key]['incomes'][] = $item;
            } else {
                $grouped[$key]['expenses'][] = $item;
            }
        }

        return array_values(array_map(static function (array $month): FinanceMonthData {
            $totalIncome = (float) array_sum(array_map(
                static fn (FinanceMonthEntryData $row): float => (float) $row->amount,
                $month['incomes'],
            ));
            $totalExpense = (float) array_sum(array_map(
                static fn (FinanceMonthEntryData $row): float => (float) $row->amount,
                $month['expenses'],
            ));

            return new FinanceMonthData(
                year: $month['year'],
                month: $month['month'],
                incomes: $month['incomes'],
                expenses: $month['expenses'],
                totalIncome: $totalIncome,
                totalExpense: $totalExpense,
                balance: $totalIncome - $totalExpense,
            );
        }, $grouped));
    }
}

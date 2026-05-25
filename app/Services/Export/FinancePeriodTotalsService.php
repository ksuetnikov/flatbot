<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Models\Object\ObjectFinanceEntry;
use App\Models\Object\ObjectModel;
use App\Support\Export\ExportPeriod;
use Illuminate\Database\Eloquent\Builder;

readonly class FinancePeriodTotals
{
    public function __construct(
        public float $totalIncome,
        public float $totalExpense,
        public float $margin,
    ) {
    }
}

class FinancePeriodTotalsService
{
    public function forObject(ObjectModel $object, ExportPeriod $period): FinancePeriodTotals
    {
        $entries = ObjectFinanceEntry::query()
            ->where('object_id', $object->getKey())
            ->when($period->from, function (Builder $query) use ($period): void {
                $fromOrdinal = ExportPeriod::financeOrdinal(
                    (int) $period->from->year,
                    (int) $period->from->month,
                    1,
                );
                $query->whereRaw(
                    '(year * 372 + month * 31 + day) >= ?',
                    [$fromOrdinal],
                );
            })
            ->when($period->to, function (Builder $query) use ($period): void {
                $toOrdinal = ExportPeriod::financeOrdinal(
                    (int) $period->to->year,
                    (int) $period->to->month,
                    (int) $period->to->day,
                );
                $query->whereRaw(
                    '(year * 372 + month * 31 + day) <= ?',
                    [$toOrdinal],
                );
            })
            ->get(['type', 'amount']);

        $totalIncome = $entries
            ->where('type', ObjectFinanceEntry::TYPE_INCOME)
            ->sum(static fn (ObjectFinanceEntry $entry): float => (float) $entry->amount);
        $totalExpense = $entries
            ->where('type', ObjectFinanceEntry::TYPE_EXPENSE)
            ->sum(static fn (ObjectFinanceEntry $entry): float => (float) $entry->amount);

        return new FinancePeriodTotals(
            totalIncome: $totalIncome,
            totalExpense: $totalExpense,
            margin: $totalIncome - $totalExpense,
        );
    }

    /**
     * @return list<list<mixed>>
     */
    public function footerRows(ExportPeriod $period, FinancePeriodTotals $totals, int $amountColumnIndex): array
    {
        $empty = array_fill(0, $amountColumnIndex + 2, '');

        $row = static function (string $label, float $amount) use ($empty, $amountColumnIndex): array {
            $line = $empty;
            $line[0] = $label;
            $line[$amountColumnIndex] = $amount;

            return $line;
        };

        $periodRow = $empty;
        $periodRow[0] = 'Итого за период ('.$period->label().'):';

        return [
            $empty,
            $periodRow,
            $row('Суммарный доход:', $totals->totalIncome),
            $row('Суммарный расход:', $totals->totalExpense),
            $row('Маржа:', $totals->margin),
        ];
    }
}

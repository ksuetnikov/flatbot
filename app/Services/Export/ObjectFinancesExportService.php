<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Models\Object\ObjectFinanceEntry;
use App\Models\Object\ObjectModel;
use App\Support\Export\ExportPeriod;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ObjectFinancesExportService
{
    private const AMOUNT_COLUMN_INDEX = 3;

    public function __construct(
        private readonly XlsxExporter $xlsxExporter,
        private readonly FinancePeriodTotalsService $financePeriodTotalsService,
    ) {
    }

    public function export(ObjectModel $object, ExportPeriod $period): StreamedResponse
    {
        $entries = $this->query($object, $period)
            ->orderBy('year')
            ->orderBy('month')
            ->orderBy('day')
            ->orderBy('id')
            ->get();

        $rows = $entries->map(static function (ObjectFinanceEntry $entry) use ($object): array {
            $date = sprintf('%02d.%02d.%04d', $entry->day, $entry->month, $entry->year);
            $typeLabel = $entry->type === ObjectFinanceEntry::TYPE_INCOME ? 'Доход' : 'Расход';

            return [
                $date,
                $object->name,
                $typeLabel,
                (float) $entry->amount,
                $entry->comment ?? '',
            ];
        })->all();

        $totals = $this->financePeriodTotalsService->forObject($object, $period);

        $filename = sprintf(
            'finansy-%s-%s.xlsx',
            $object->id,
            now()->format('Y-m-d'),
        );

        return $this->xlsxExporter->download(
            filename: $filename,
            sheetTitle: 'Финансы',
            headers: ['Дата', 'Объект', 'Тип', 'Сумма', 'Комментарий'],
            rows: $rows,
            footerRows: $this->financePeriodTotalsService->footerRows(
                $period,
                $totals,
                self::AMOUNT_COLUMN_INDEX,
            ),
        );
    }

    /**
     * @return Builder<ObjectFinanceEntry>
     */
    private function query(ObjectModel $object, ExportPeriod $period): Builder
    {
        return ObjectFinanceEntry::query()
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
            });
    }
}

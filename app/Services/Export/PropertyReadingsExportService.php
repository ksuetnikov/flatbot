<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Models\Object\ObjectModel;
use App\Models\Object\ObjectProperty;
use App\Models\Object\PropertyReading;
use App\Support\Export\ExportPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyReadingsExportService
{
    private const AMOUNT_COLUMN_INDEX = 2;

    public function __construct(
        private readonly XlsxExporter $xlsxExporter,
        private readonly FinancePeriodTotalsService $financePeriodTotalsService,
    ) {
    }

    public function export(
        ObjectModel $object,
        ObjectProperty $property,
        ExportPeriod $period,
    ): StreamedResponse {
        $readings = $this->query($property, $period)
            ->orderBy('recorded_at')
            ->get();

        $meterLabel = $property->code
            ? $property->name.' ('.$property->code.')'
            : $property->name;

        $rows = $readings->map(static fn (PropertyReading $reading): array => [
            $reading->recorded_at->timezone(config('app.timezone'))->format('d.m.Y H:i'),
            $object->name,
            (string) $reading->value,
            $meterLabel,
        ])->all();

        $totals = $this->financePeriodTotalsService->forObject($object, $period);

        $filename = sprintf(
            'pokazaniya-%s-%s-%s.xlsx',
            $object->id,
            $property->id,
            now()->format('Y-m-d'),
        );

        return $this->xlsxExporter->download(
            filename: $filename,
            sheetTitle: 'Показания',
            headers: ['Дата', 'Объект', 'Показание', 'Счётчик'],
            rows: $rows,
            footerRows: $this->financePeriodTotalsService->footerRows(
                $period,
                $totals,
                self::AMOUNT_COLUMN_INDEX,
            ),
        );
    }

    /**
     * @return Builder<PropertyReading>
     */
    private function query(ObjectProperty $property, ExportPeriod $period): Builder
    {
        return PropertyReading::query()
            ->where('object_property_id', $property->getKey())
            ->when($period->from, static fn (Builder $query, Carbon $from): Builder => $query->where('recorded_at', '>=', $from))
            ->when($period->to, static fn (Builder $query, Carbon $to): Builder => $query->where('recorded_at', '<=', $to));
    }
}

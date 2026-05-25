<?php

declare(strict_types=1);

namespace App\Services\Object;

use App\Models\Object\ObjectFinanceEntry;
use App\Models\Object\ObjectModel;
use Illuminate\Support\Collection;

class ObjectFinanceSummaryService
{
    /**
     * @param Collection<int, ObjectModel> $objects
     *
     * @return array<int, array{total_income: float, total_expense: float, balance: float, entries_count: int}>
     */
    public function summariesForObjects(Collection $objects): array
    {
        if ($objects->isEmpty()) {
            return [];
        }

        $objectIds = $objects->pluck('id')->all();

        $rows = ObjectFinanceEntry::query()
            ->whereIn('object_id', $objectIds)
            ->get(['object_id', 'type', 'amount']);

        $summaries = [];

        foreach ($objectIds as $objectId) {
            $summaries[$objectId] = [
                'total_income' => 0.0,
                'total_expense' => 0.0,
                'balance' => 0.0,
                'entries_count' => 0,
            ];
        }

        foreach ($rows as $row) {
            $objectId = $row->object_id;
            $amount = (float) $row->amount;
            $summaries[$objectId]['entries_count']++;

            if ($row->type === ObjectFinanceEntry::TYPE_INCOME) {
                $summaries[$objectId]['total_income'] += $amount;
            } else {
                $summaries[$objectId]['total_expense'] += $amount;
            }
        }

        foreach ($summaries as $objectId => $summary) {
            $summaries[$objectId]['balance'] = $summary['total_income'] - $summary['total_expense'];
        }

        return $summaries;
    }
}

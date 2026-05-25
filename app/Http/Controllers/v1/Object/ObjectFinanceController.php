<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreObjectFinanceEntryRequest;
use App\Models\Object\ObjectFinanceEntry;
use App\Models\Object\ObjectModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ObjectFinanceController extends Controller
{
    public function show(ObjectModel $object): Response
    {
        $entries = $object->financeEntries()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('day')
            ->orderBy('type')
            ->orderBy('id')
            ->get();

        return Inertia::render('object/finances', [
            'object' => [
                'id' => $object->id,
                'name' => $object->name,
            ],
            'months' => $this->groupEntriesByMonth($entries),
        ]);
    }

    public function store(StoreObjectFinanceEntryRequest $request, ObjectModel $object): RedirectResponse
    {
        $validated = $request->validated();

        $object->financeEntries()->create([
            'year' => (int) $validated['year'],
            'month' => (int) $validated['month'],
            'day' => (int) $validated['day'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Запись добавлена');
    }

    public function destroy(ObjectModel $object, int $entryId): RedirectResponse
    {
        $entry = ObjectFinanceEntry::query()
            ->where('object_id', $object->getKey())
            ->findOrFail($entryId);

        $entry->delete();

        return redirect()->back()->with('success', 'Запись удалена');
    }

    /**
     * @param Collection<int, ObjectFinanceEntry> $entries
     *
     * @return list<array<string, mixed>>
     */
    private function groupEntriesByMonth(Collection $entries): array
    {
        $grouped = [];

        foreach ($entries as $entry) {
            $key = $entry->year.'-'.$entry->month;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'year' => $entry->year,
                    'month' => $entry->month,
                    'incomes' => [],
                    'expenses' => [],
                ];
            }

            $item = [
                'id' => $entry->id,
                'day' => $entry->day,
                'amount' => (string) $entry->amount,
                'comment' => $entry->comment,
            ];

            if ($entry->type === ObjectFinanceEntry::TYPE_INCOME) {
                $grouped[$key]['incomes'][] = $item;
            } else {
                $grouped[$key]['expenses'][] = $item;
            }
        }

        return array_values(array_map(static function (array $month): array {
            $totalIncome = array_sum(array_map(
                static fn (array $row): float => (float) $row['amount'],
                $month['incomes'],
            ));
            $totalExpense = array_sum(array_map(
                static fn (array $row): float => (float) $row['amount'],
                $month['expenses'],
            ));

            return [
                ...$month,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'balance' => $totalIncome - $totalExpense,
            ];
        }, $grouped));
    }
}

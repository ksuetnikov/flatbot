<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\ExportPeriodRequest;
use App\Http\Requests\Object\StoreObjectFinanceEntryRequest;
use App\Models\Object\ObjectModel;
use App\Services\Export\ObjectFinancesExportService;
use App\Services\Object\Data\FinanceObjectHeadingData;
use App\Services\Object\ObjectFinanceEntryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ObjectFinanceController extends Controller
{
    public function __construct(
        private readonly ObjectFinancesExportService $objectFinancesExportService,
        private readonly ObjectFinanceEntryService $objectFinanceEntryService,
    ) {}

    public function export(ExportPeriodRequest $request, ObjectModel $object): StreamedResponse
    {
        return $this->objectFinancesExportService->export($object, $request->toExportPeriod());
    }

    public function show(ObjectModel $object): Response
    {
        return Inertia::render('object/finances', [
            'object' => new FinanceObjectHeadingData($object->id, $object->name),
            'months' => $this->objectFinanceEntryService->months($object),
        ]);
    }

    public function store(StoreObjectFinanceEntryRequest $request, ObjectModel $object): RedirectResponse
    {
        $validated = $request->validated();
        $comment = $validated['comment'] ?? null;

        $this->objectFinanceEntryService->create(
            object: $object,
            year: (int) $validated['year'],
            month: (int) $validated['month'],
            day: (int) $validated['day'],
            type: (string) $validated['type'],
            amount: $this->amount($validated['amount']),
            comment: is_string($comment) ? $comment : null,
        );

        return redirect()->back()->with('success', 'Запись добавлена');
    }

    public function destroy(ObjectModel $object, int $entryId): RedirectResponse
    {
        $this->objectFinanceEntryService->delete($object, $entryId);

        return redirect()->back()->with('success', 'Запись удалена');
    }

    private function amount(mixed $amount): string|int|float
    {
        if (is_string($amount) || is_int($amount) || is_float($amount)) {
            return $amount;
        }

        return (string) $amount;
    }
}

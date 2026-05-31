<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\UpdateObjectRequest;
use App\Models\Object\ObjectModel;
use App\Services\Object\ObjectFinanceSummaryService;
use App\Services\Object\ObjectService;
use App\Services\SubscribeUser\SubscribeUserService;
use Auth;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * @author Kirill Suetnikov
 */
class ObjectController extends Controller
{
    public function __construct(
        private readonly ObjectService $objectService,
        private readonly ObjectFinanceSummaryService $financeSummaryService,
        private readonly SubscribeUserService $subscribeUserService,
    ) {
    }

    /** Обзор объекта: счётчики и последние показания */
    public function show(ObjectModel $object): Response
    {
        $object->load([
            'subscribers',
            'properties.latestReading',
        ]);

        $financeSummaries = $this->financeSummaryService->summariesForObjects(collect([$object]));

        return Inertia::render('object/show', [
            'object' => [
                'id' => $object->id,
                'name' => $object->name,
                'description' => $object->description,
                'notification_date' => $object->notification_date,
                'created_at' => $object->created_at,
                'subscribers' => $object->subscribers,
                'finance_summary' => $financeSummaries[$object->id] ?? [
                    'total_income' => 0,
                    'total_expense' => 0,
                    'balance' => 0,
                    'entries_count' => 0,
                ],
                'properties' => $object->properties->map(static fn ($property) => [
                    'id' => $property->id,
                    'name' => $property->name,
                    'code' => $property->code,
                    'description' => $property->description,
                    'latest_reading' => $property->latestReading ? [
                        'value' => $property->latestReading->value,
                        'recorded_at' => $property->latestReading->recorded_at,
                    ] : null,
                ]),
            ],
        ]);
    }

    /** Настройки объекта (подписчики, счётчики) */
    public function edit(ObjectModel $object): Response
    {
        $this->subscribeUserService->backfillTelegramUsers($object->getKey());
        $object->load(['subscribers.telegramUser', 'properties']);

        return Inertia::render('object/edit', [
            'object' => [
                'id' => $object->id,
                'name' => $object->name,
                'description' => $object->description,
                'notification_date' => $object->notification_date,
                'subscribers' => $this->mapSubscribers($object),
                'properties' => $object->properties,
            ],
            'allSubscribers' => $this->mapSubscribers($object),
            'allProperties' => $object->properties,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapSubscribers(ObjectModel $object): array
    {
        return $object->subscribers->map(static fn ($subscriber) => [
            'id' => $subscriber->id,
            'name' => $subscriber->name,
            'registrationKey' => $subscriber->telegramUser?->registration_key,
            'isRegistered' => $subscriber->telegramUser?->isRegistered() ?? false,
            'telegramUsername' => $subscriber->telegramUser?->username,
        ])->values()->all();
    }

    /** Создание объекта
     *
     * @return RedirectResponse
     */
    public function create(): RedirectResponse
    {
        $user = Auth::getUser();
        $object = $this->objectService->create($user);

        return redirect()->route('object.show', $object)
            ->with('success', 'Объект успешно создан');
    }

    /** Само редактирование
     *
     * @param UpdateObjectRequest $request
     * @param ObjectModel $object
     *
     * @return RedirectResponse
     */

    public function update(UpdateObjectRequest $request, ObjectModel $object): RedirectResponse
    {
        // $this->authorize('update', $object);

        $object->update($request->validated());

        return redirect()->route('object.show', $object)
            ->with('success', 'Объект успешно обновлен');
    }

    /** Удаление объекта
     *
     * @param ObjectModel $object
     *
     * @return RedirectResponse
     */
    public function destroy(ObjectModel $object): RedirectResponse
    {
        // $this->authorize('delete', $object);

        $object->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Объект успешно удален');
    }
}

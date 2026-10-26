<?php

declare(strict_types=1);

namespace App\Services\Object;

use App\Models\Object\ObjectModel;
use App\Models\Object\ObjectProperty;
use App\Models\Object\PropertyReading;
use App\Models\SubscribeUser\SubscribeUser;
use App\Models\User;
use App\Services\Object\Data\DashboardObjectData;
use App\Services\Object\Data\FinanceSummaryData;
use App\Services\Object\Data\ObjectEditData;
use App\Services\Object\Data\ObjectEditSubscriberData;
use App\Services\Object\Data\ObjectShowData;
use App\Services\Object\Data\ObjectShowLatestReadingData;
use App\Services\Object\Data\ObjectShowPropertyData;
use App\Services\SubscribeUser\SubscribeUserService;
use Illuminate\Support\Carbon;

/**
 * Собирает данные дашборда, карточки и настроек объекта для Inertia.
 */
class ObjectPageService
{
    public function __construct(
        private readonly ObjectFinanceSummaryService $financeSummaryService,
        private readonly SubscribeUserService $subscribeUserService,
        private readonly ReadingSubmissionStatusService $readingSubmissionStatusService,
    ) {}

    /**
     * Объекты текущего пользователя со сводкой финансов.
     *
     * @return list<DashboardObjectData>
     */
    public function dashboardObjects(User $user): array
    {
        $objects = $user->objects;
        $summaries = $this->financeSummaryService->summariesForObjects($objects);

        return $objects
            ->map(function (ObjectModel $object) use ($summaries): DashboardObjectData {
                return new DashboardObjectData(
                    id: $object->id,
                    name: $object->name,
                    description: $object->description,
                    notificationDate: $object->notification_date,
                    createdAt: $object->created_at,
                    updatedAt: $object->updated_at,
                    financeSummary: $this->summaryFor($summaries, $object->id),
                );
            })
            ->values()
            ->all();
    }

    /**
     * Обзор объекта: подписчики, счётчики, финансы и статус сдачи показаний.
     */
    public function show(ObjectModel $object): ObjectShowData
    {
        $object->load([
            'subscribers',
            'properties.latestReading',
        ]);

        $summaries = $this->financeSummaryService->summariesForObjects(collect([$object]));

        return new ObjectShowData(
            id: $object->id,
            name: $object->name,
            description: $object->description,
            notificationDate: $object->notification_date,
            createdAt: $object->created_at,
            subscribers: $object->subscribers,
            financeSummary: $this->summaryFor($summaries, $object->id),
            properties: $object->properties
                ->map(fn (ObjectProperty $property): ObjectShowPropertyData => $this->showProperty($property))
                ->values()
                ->all(),
            readingStatus: $this->readingSubmissionStatusService->forObject($object),
        );
    }

    /**
     * Настройки объекта. Подписчикам без слота Telegram слот создаётся до отрисовки.
     */
    public function edit(ObjectModel $object): ObjectEditData
    {
        $this->subscribeUserService->backfillTelegramUsers($object->id);
        $object->load(['subscribers.telegramUser', 'properties']);

        return new ObjectEditData(
            id: $object->id,
            name: $object->name,
            description: $object->description,
            notificationDate: $object->notification_date,
            subscribers: $this->editSubscribers($object),
            properties: $object->properties,
        );
    }

    /**
     * @param  array<int, array{total_income: float, total_expense: float, balance: float, entries_count: int}>  $summaries
     */
    private function summaryFor(array $summaries, int $objectId): FinanceSummaryData
    {
        if (! isset($summaries[$objectId])) {
            return FinanceSummaryData::empty();
        }

        return FinanceSummaryData::fromSummary($summaries[$objectId]);
    }

    private function showProperty(ObjectProperty $property): ObjectShowPropertyData
    {
        return new ObjectShowPropertyData(
            id: $property->id,
            name: $property->name,
            code: $property->code,
            description: $property->description,
            latestReading: $this->latestReading($property->latestReading),
        );
    }

    private function latestReading(?PropertyReading $reading): ?ObjectShowLatestReadingData
    {
        if ($reading === null) {
            return null;
        }

        $recordedAt = $reading->recorded_at;
        if (! $recordedAt instanceof Carbon) {
            return null;
        }

        return new ObjectShowLatestReadingData(
            value: (string) $reading->value,
            recordedAt: $recordedAt,
        );
    }

    /**
     * @return list<ObjectEditSubscriberData>
     */
    private function editSubscribers(ObjectModel $object): array
    {
        return $object->subscribers
            ->map(static function (SubscribeUser $subscriber): ObjectEditSubscriberData {
                $telegramUser = $subscriber->telegramUser;

                return new ObjectEditSubscriberData(
                    id: $subscriber->id,
                    name: $subscriber->name,
                    registrationKey: $telegramUser?->registration_key,
                    isRegistered: $telegramUser?->isRegistered() ?? false,
                    telegramUsername: $telegramUser?->username,
                );
            })
            ->values()
            ->all();
    }
}

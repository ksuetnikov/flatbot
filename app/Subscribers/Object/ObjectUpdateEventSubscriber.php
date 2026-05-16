<?php

declare(strict_types=1);

namespace App\Subscribers\Object;

use App\Events\Object\AfterUpdateObjectEvent;
use App\Services\Object\JobObjectUpdateService;
use Illuminate\Events\Dispatcher;

/**
 * @author Kirill Suetnikov
 */
readonly class ObjectUpdateEventSubscriber
{
    /**
     * @param JobObjectUpdateService $jobObjectUpdateService
     */
    public function __construct(
        private JobObjectUpdateService $jobObjectUpdateService,
    ) {}

    /**
     * @param Dispatcher $events
     *
     * @return string[]
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            AfterUpdateObjectEvent::class => 'handleUpdatingDigestStructure',
        ];
    }

    /**
     * @param AfterUpdateObjectEvent $event
     *
     * @return void
     */
    public function handleUpdatingDigestStructure(AfterUpdateObjectEvent $event): void
    {
        if (!config('services.go_api.object_post_url')) {
            return;
        }

        $this
            ->jobObjectUpdateService
            ->runUpdateObjectJob($event->eventDto);
    }
}

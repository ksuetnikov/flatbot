<?php

declare(strict_types=1);

namespace App\Jobs\Object;

use App\DTO\Object\AfterUpdateObjectDTO;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * @author Kirill Suetnikov
 */
class UpdateObjectJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var Carbon */
    public Carbon $date;

    /**
     * @param AfterUpdateObjectDTO $dto
     */
    public function __construct(
        private readonly AfterUpdateObjectDTO $dto,
    ) {
        $this->date = now();
    }

    /**
     * @return void
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->sendInfoToOtherServices();
    }

    /**
     * @return void
     */
    public function sendInfoToOtherServices(): void
    {
        \Log::info('Обновилось!');
        \Log::info('id is:');
        \Log::info($this->dto->objectId);
    }
}

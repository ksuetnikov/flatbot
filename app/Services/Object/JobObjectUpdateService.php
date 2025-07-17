<?php

declare(strict_types=1);

namespace App\Services\Object;

use App\DTO\Object\AfterUpdateObjectDTO;
use App\Jobs\Object\UpdateObjectJob;

/**
 * @author Kirill Suetnikov
 */
readonly class JobObjectUpdateService
{
    /**
     * @param AfterUpdateObjectDTO $updateObjectDTO
     *
     * @return void
     */
    public function runUpdateObjectJob(AfterUpdateObjectDTO $updateObjectDTO): void
    {
        UpdateObjectJob::dispatch($updateObjectDTO);
    }
}

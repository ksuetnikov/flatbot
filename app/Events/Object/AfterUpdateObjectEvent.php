<?php

declare(strict_types=1);

namespace App\Events\Object;

use App\DTO\Object\AfterUpdateObjectDTO;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * @author Kirill Suetnikov
 */
class AfterUpdateObjectEvent
{
    use Dispatchable;

    /**
     * @param AfterUpdateObjectDTO $eventDto
     */
    public function __construct(
        public AfterUpdateObjectDTO $eventDto,
    ) {}
}

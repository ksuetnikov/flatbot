<?php

declare(strict_types=1);

namespace App\DTO\ObjectProperty;

use App\DTO\BaseDTO;

/**
 * @author Kirill Suetnikov
 */
class ObjectPropertyDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public ?string $code,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\DTO\SubscribeUser;

use App\DTO\BaseDTO;

/**
 * @author Kirill Suetnikov
 */
class SubscribeUserDTO extends BaseDTO
{
    public function __construct(
        public string $name,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Http\DTO\SubscribeUser;

use App\Http\DTO\BaseDTO;

/**
 * @author Kirill Suetnikov
 */
class SubscribeUserDTO extends BaseDTO
{
    public function __construct(
        public string $name,
    ) {}
}

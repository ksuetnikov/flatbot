<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Заголовок объекта на странице финансов.
 *
 * @implements Arrayable<string, int|string>
 */
final readonly class FinanceObjectHeadingData implements Arrayable
{
    /**
     * @param  int  $id  Идентификатор объекта
     * @param  string  $name  Название объекта
     */
    public function __construct(
        public int $id,
        public string $name,
    ) {}

    /**
     * @return array{id: int, name: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}

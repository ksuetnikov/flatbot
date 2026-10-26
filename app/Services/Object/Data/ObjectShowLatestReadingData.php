<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

/**
 * Последнее показание счётчика на карточке объекта.
 *
 * @implements Arrayable<string, string|Carbon>
 */
final readonly class ObjectShowLatestReadingData implements Arrayable
{
    /**
     * @param  string  $value  Показание, decimal с 4 знаками
     * @param  Carbon  $recordedAt  Когда показание снято
     */
    public function __construct(
        public string $value,
        public Carbon $recordedAt,
    ) {}

    /**
     * @return array{value: string, recorded_at: Carbon}
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'recorded_at' => $this->recordedAt,
        ];
    }
}

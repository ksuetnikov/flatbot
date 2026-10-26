<?php

declare(strict_types=1);

namespace App\Services\Object\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

/**
 * Счётчик в обзоре объекта.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class ObjectShowPropertyData implements Arrayable
{
    /**
     * @param  int  $id  Идентификатор счётчика
     * @param  string  $name  Название
     * @param  string|null  $code  Код счётчика
     * @param  string|null  $description  Описание
     * @param  ObjectShowLatestReadingData|null  $latestReading  Последняя запись истории или null, если показаний нет
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $code,
        public ?string $description,
        public ?ObjectShowLatestReadingData $latestReading,
    ) {}

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     code: string|null,
     *     description: string|null,
     *     latest_reading: array{value: string, recorded_at: Carbon}|null
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'latest_reading' => $this->latestReading?->toArray(),
        ];
    }
}

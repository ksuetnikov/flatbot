<?php
namespace App\Entities;

class ObjectPropertyEntity  implements  \JsonSerializable
{
    public function __construct(
        public int $id,
        public string $name,
        public string $code,
        public ?string $description,
        public int $object_id,
        public int $order,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $deleted_at,
    ) {}

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}

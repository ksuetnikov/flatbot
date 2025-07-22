<?php
namespace App\Entities;

class ObjectEntity implements \JsonSerializable
{
    /**
     * @param array<SubscriberEntity> $subscribers
     * @param array<ObjectPropertyEntity> $properties
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public int $user_id,
        public int $order,
        public ?string $notification_date,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $deleted_at,
        public array $subscribers = [],
        public array $properties = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $subscribers = [];
        if (!empty($data['subscribers']) && is_array($data['subscribers'])) {
            foreach ($data['subscribers'] as $subscriber) {
                $subscribers[] = new SubscriberEntity(
                    $subscriber['id'],
                    $subscriber['name'],
                    $subscriber['description'] ?? null,
                    $subscriber['object_id'],
                    $subscriber['order'],
                    $subscriber['created_at'] ?? null,
                    $subscriber['updated_at'] ?? null,
                    $subscriber['deleted_at'] ?? null,
                );
            }
        }

        $properties = [];
        if (!empty($data['properties']) && is_array($data['properties'])) {
            foreach ($data['properties'] as $property) {
                $properties[] = new ObjectPropertyEntity(
                    $property['id'],
                    $property['name'],
                    $property['code'],
                    $property['description'] ?? null,
                    $property['object_id'],
                    $property['order'],
                    $property['created_at'] ?? null,
                    $property['updated_at'] ?? null,
                    $property['deleted_at'] ?? null,
                );
            }
        }

        return new self(
            $data['id'],
            $data['name'],
            $data['description'] ?? null,
            $data['user_id'],
            $data['order'],
            $data['notification_date'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
            $data['deleted_at'] ?? null,
            $subscribers,
            $properties
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'order' => $this->order,
            'notification_date' => $this->notification_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'subscribers' => $this->subscribers,
            'properties' => $this->properties,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Object;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $object_property_id
 * @property string $value
 * @property Carbon $recorded_at
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property-read ObjectProperty $objectProperty
 */
class PropertyReading extends Model
{
    public $table = 'property_readings';

    protected $fillable = [
        'object_property_id',
        'value',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'recorded_at' => 'datetime',
        ];
    }

    public function objectProperty(): BelongsTo
    {
        return $this->belongsTo(ObjectProperty::class, 'object_property_id', 'id');
    }

    public function photo(): HasOne
    {
        return $this->hasOne(ObjectPropertyFile::class, 'property_reading_id', 'id');
    }
}

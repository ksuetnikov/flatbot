<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Ежемесячное расписание уведомлений: день месяца + время без часового пояса.
 * Значение в БД и API — «настенные часы», а не UTC-момент.
 */
class MonthlyNotificationSchedule implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (preg_match('/^(\d{4}-\d{2}-\d{2})[T ](\d{2}):(\d{2})/', (string) $value, $matches) !== 1) {
            return (string) $value;
        }

        return "{$matches[1]}T{$matches[2]}:{$matches[3]}:00";
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (preg_match('/^(\d{4}-\d{2}-\d{2})[T ](\d{2}):(\d{2})/', (string) $value, $matches) !== 1) {
            return (string) $value;
        }

        return "{$matches[1]} {$matches[2]}:{$matches[3]}:00";
    }
}

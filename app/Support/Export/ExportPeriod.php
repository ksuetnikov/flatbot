<?php

declare(strict_types=1);

namespace App\Support\Export;

use Illuminate\Support\Carbon;

readonly class ExportPeriod
{
    public function __construct(
        public ?Carbon $from = null,
        public ?Carbon $to = null,
    ) {
    }

    public function isFullPeriod(): bool
    {
        return $this->from === null && $this->to === null;
    }

    public function label(): string
    {
        if ($this->isFullPeriod()) {
            return 'весь период';
        }

        $fromLabel = $this->from !== null
            ? $this->from->locale('ru')->translatedFormat('F Y')
            : null;
        $toLabel = $this->to !== null
            ? $this->to->locale('ru')->translatedFormat('F Y')
            : null;

        if ($fromLabel !== null && $toLabel !== null) {
            return $fromLabel.' — '.$toLabel;
        }

        if ($fromLabel !== null) {
            return 'с '.$fromLabel;
        }

        return 'по '.$toLabel;
    }

    public static function monthStart(int $year, int $month): Carbon
    {
        return Carbon::create($year, $month, 1)->startOfDay();
    }

    public static function monthEnd(int $year, int $month): Carbon
    {
        return Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
    }

    public static function financeOrdinal(int $year, int $month, int $day): int
    {
        return $year * 372 + $month * 31 + $day;
    }
}

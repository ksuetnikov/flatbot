<?php

declare(strict_types=1);

namespace App\Http\Requests\Object;

use App\Http\Requests\BaseRequest;
use App\Support\Export\ExportPeriod;
use Illuminate\Contracts\Validation\Validator;
class ExportPeriodRequest extends BaseRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'year_from' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'month_from' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year_to' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'month_to' => ['nullable', 'integer', 'min:1', 'max:12'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hasFrom = $this->filled('year_from') || $this->filled('month_from');
            $hasTo = $this->filled('year_to') || $this->filled('month_to');

            if ($this->filled('month_from') && ! $this->filled('year_from')) {
                $validator->errors()->add('year_from', 'Укажите год начала периода.');
            }
            if ($this->filled('year_from') && ! $this->filled('month_from')) {
                $validator->errors()->add('month_from', 'Укажите месяц начала периода.');
            }
            if ($this->filled('month_to') && ! $this->filled('year_to')) {
                $validator->errors()->add('year_to', 'Укажите год окончания периода.');
            }
            if ($this->filled('year_to') && ! $this->filled('month_to')) {
                $validator->errors()->add('month_to', 'Укажите месяц окончания периода.');
            }

            if ($hasFrom && $hasTo) {
                $from = ExportPeriod::monthStart(
                    (int) $this->input('year_from'),
                    (int) $this->input('month_from'),
                );
                $to = ExportPeriod::monthEnd(
                    (int) $this->input('year_to'),
                    (int) $this->input('month_to'),
                );

                if ($from->greaterThan($to)) {
                    $validator->errors()->add('month_to', 'Конец периода не может быть раньше начала.');
                }
            }
        });
    }

    public function toExportPeriod(): ExportPeriod
    {
        $from = null;
        $to = null;

        if ($this->filled('year_from') && $this->filled('month_from')) {
            $from = ExportPeriod::monthStart(
                (int) $this->input('year_from'),
                (int) $this->input('month_from'),
            );
        }

        if ($this->filled('year_to') && $this->filled('month_to')) {
            $to = ExportPeriod::monthEnd(
                (int) $this->input('year_to'),
                (int) $this->input('month_to'),
            );
        }

        return new ExportPeriod($from, $to);
    }
}

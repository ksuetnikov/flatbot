<?php

declare(strict_types=1);

namespace App\Http\Requests\Object;

use App\Http\Requests\BaseRequest;
use App\Models\Object\ObjectFinanceEntry;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreObjectFinanceEntryRequest extends BaseRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'day' => ['required', 'integer', 'min:1', 'max:31'],
            'type' => ['required', 'string', Rule::in([ObjectFinanceEntry::TYPE_INCOME, ObjectFinanceEntry::TYPE_EXPENSE])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'comment' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $year = (int) $this->input('year');
            $month = (int) $this->input('month');
            $day = (int) $this->input('day');

            if ($year && $month && $day && ! checkdate($month, $day, $year)) {
                $validator->errors()->add('day', 'Указан некорректный день для выбранного месяца.');
            }
        });
    }
}

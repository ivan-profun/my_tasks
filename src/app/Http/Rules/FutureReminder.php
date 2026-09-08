<?php

namespace App\Http\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class FutureReminder implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return;
        }

        $date = Carbon::parse($value);

        if (!$date->isFuture()) {
            $fail('Напоминание должно быть установлено на будущее время.');
        }
    }
}

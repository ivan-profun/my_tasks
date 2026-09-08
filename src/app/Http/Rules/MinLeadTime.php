<?php

namespace App\Http\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class MinLeadTime implements ValidationRule
{
    public function __construct(private readonly int $minutes = 15)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return;
        }

        $date = Carbon::parse($value);
        $threshold = now()->addMinutes($this->minutes);

        if ($date->lt($threshold)) {
            $fail("Напоминание должно быть установлено минимум за {$this->minutes} минут от текущего момента.");
        }
    }
}

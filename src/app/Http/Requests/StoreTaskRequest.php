<?php

namespace App\Http\Requests;

use App\Http\Rules\FutureReminder;
use App\Http\Rules\MinLeadTime;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'reminder_at' => ['nullable', 'date', new FutureReminder(), new MinLeadTime(15)],
        ];
    }
}

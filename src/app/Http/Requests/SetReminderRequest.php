<?php

namespace App\Http\Requests;

use App\Http\Rules\FutureReminder;
use Illuminate\Foundation\Http\FormRequest;

class SetReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reminder_at' => ['required', 'date', new FutureReminder()],
        ];
    }
}

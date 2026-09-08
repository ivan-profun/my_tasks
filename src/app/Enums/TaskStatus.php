<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Ожидает',
            self::Completed => 'Завершёна',
        };
    }
}

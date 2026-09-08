<?php

namespace App\Exceptions;

use Exception;

class ReminderLimitExceededException extends Exception
{
    public function __construct(string $message = 'Достигнут лимит активных напоминаний (3 на пользователя).')
    {
        parent::__construct($message);
    }
}

<?php

namespace App\Services;

use App\Exceptions\ReminderLimitExceededException;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReminderService
{
    private const ACTIVE_REMINDER_LIMIT = 3;

    /**
     * @throws ReminderLimitExceededException
     */
    public function setReminder(Task $task, Carbon $reminderAt): Task
    {
        return DB::transaction(function () use ($task, $reminderAt) {
            /** @var User $lockedUser */
            $lockedUser = User::whereKey($task->user_id)->lockForUpdate()->first();

            $activeCount = Task::query()
                ->where('user_id', $lockedUser->id)
                ->whereNotNull('reminder_at')
                // Текущая задача не учитывается в лимите
                ->where('id', '!=', $task->id)
                ->count();

            if ($activeCount >= self::ACTIVE_REMINDER_LIMIT) {
                throw new ReminderLimitExceededException();
            }

            $task->update(['reminder_at' => $reminderAt]);

            return $task->refresh();
        });
    }

    public function clearReminder(Task $task): Task
    {
        $task->update(['reminder_at' => null]);

        return $task;
    }
}

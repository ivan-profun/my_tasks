<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\SetReminderRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\ReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    public function __construct(private readonly ReminderService $reminders)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tasks = $request->user()
            ->tasks()
            ->latest()
            ->get();

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $reminderAt = $data['reminder_at'] ?? null;
        unset($data['reminder_at']);

        $task = $request->user()->tasks()->create([
            ...$data,
            'status' => TaskStatus::Pending,
        ]);

        if ($reminderAt !== null) {
            $task = $this->reminders->setReminder($task, Carbon::parse($reminderAt));
        }

        return (new TaskResource($task))->response()->setStatusCode(201);
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        if (array_key_exists('title', $validated)) {
            $task->title = $validated['title'];
        }

        if (array_key_exists('description', $validated)) {
            $task->description = $validated['description'];
        }

        if (array_key_exists('status', $validated)) {
            $task->status = $validated['status'];

            if ($task->status === TaskStatus::Completed) {
                $task->reminder_at = null;
            }
        }

        $task->save();

        if ($request->has('reminder_at')) {
            $value = $request->input('reminder_at');

            if ($task->status === TaskStatus::Completed) {
                if ($value !== null) {
                    abort(422, 'Нельзя установить напоминание для завершённой задачи.');
                }
            } elseif ($value === null) {
                $task = $this->reminders->clearReminder($task);
            } else {
                $task = $this->reminders->setReminder($task, Carbon::parse($value));
            }
        }

        return new TaskResource($task->refresh());
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(null, 204);
    }

    public function setReminder(SetReminderRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        if ($task->status === TaskStatus::Completed) {
            abort(422, 'Нельзя установить напоминание для завершённой задачи.');
        }

        $task = $this->reminders->setReminder(
            $task,
            Carbon::parse($request->validated('reminder_at'))
        );

        return new TaskResource($task);
    }

    public function deleteReminder(Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $task = $this->reminders->clearReminder($task);

        return new TaskResource($task);
    }
}

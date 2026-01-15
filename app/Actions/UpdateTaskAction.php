<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

class UpdateTaskAction
{
    public function execute(
        Task $task,
        string $description,
        ?string $dueDate = null,
        ?bool $isCompleted = null
    ): Task {
        $task->fill([
            'description' => $description,
            'is_completed' => $isCompleted ?? $task->is_completed,
        ]);

        if ($dueDate !== null) {
            $task->due_date = $dueDate;
        }

        $task->save();

        return $task->fresh();
    }
}

<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

class UpdateNextReminderAction
{
    public function execute(Task $task, ?string $nextReminder): Task
    {
        $task->fill([
            'next_reminder' => $nextReminder,
        ]);
        $task->save();

        return $task->fresh();
    }
}

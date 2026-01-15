<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;
use App\Models\User;

class CreateTaskAction
{
    public function execute(User $user, string $description, ?string $dueDate = null): Task
    {
        return Task::create([
            'user_id' => $user->id,
            'description' => $description,
            'due_date' => $dueDate,
            'is_completed' => false,
            'comments' => [],
        ]);
    }
}

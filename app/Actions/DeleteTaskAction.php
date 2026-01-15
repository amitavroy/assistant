<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

class DeleteTaskAction
{
    public function execute(Task $task): bool
    {
        $deleted = $task->delete();

        return (bool) $deleted;
    }
}

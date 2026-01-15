<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;

class AddCommentAction
{
    public function execute(Task $task, string $comment): Task
    {
        $comments = $task->comments ?? [];
        $comments[] = $comment;

        $task->fill([
            'comments' => $comments,
        ]);
        $task->save();

        return $task->fresh();
    }
}

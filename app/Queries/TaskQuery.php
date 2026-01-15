<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

class TaskQuery
{
    public function forUser(int $userId, bool $showCompleted = false): Builder
    {
        return Task::query()
            ->where('user_id', $userId)
            ->when(! $showCompleted, fn ($query) => $query->where('is_completed', false))
            ->when($showCompleted, fn ($query) => $query->where('is_completed', true))
            ->orderBy('created_at', 'desc');
    }
}

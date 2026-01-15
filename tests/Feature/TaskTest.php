<?php

use App\Models\Task;
use App\Models\User;

test('task has correct fillable attributes', function () {
    $task = new Task;

    $fillable = $task->getFillable();

    expect($fillable)->toContain('user_id');
    expect($fillable)->toContain('description');
    expect($fillable)->toContain('is_completed');
    expect($fillable)->toContain('due_date');
    expect($fillable)->toContain('comments');
    expect($fillable)->toContain('next_reminder');
});

test('task casts is_completed to boolean', function () {
    $task = Task::factory()->create([
        'is_completed' => 1,
    ]);

    expect($task->is_completed)->toBeTrue();
    expect($task->is_completed)->toBeBool();

    $task->update(['is_completed' => 0]);
    expect($task->fresh()->is_completed)->toBeFalse();
});

test('task casts due_date to date', function () {
    $task = Task::factory()->create([
        'due_date' => '2024-12-31',
    ]);

    expect($task->due_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    expect($task->due_date->format('Y-m-d'))->toBe('2024-12-31');
});

test('task casts comments to array', function () {
    $task = Task::factory()->create([
        'comments' => ['Comment 1', 'Comment 2', 'Comment 3'],
    ]);

    expect($task->comments)->toBeArray();
    expect($task->comments)->toHaveCount(3);
    expect($task->comments[0])->toBe('Comment 1');
});

test('task casts next_reminder to datetime', function () {
    $task = Task::factory()->create([
        'next_reminder' => '2024-12-31 10:30:00',
    ]);

    expect($task->next_reminder)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    expect($task->next_reminder->format('Y-m-d H:i:s'))->toBe('2024-12-31 10:30:00');
});

test('task belongs to user', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($task->user)->toBeInstanceOf(User::class);
    expect($task->user->id)->toBe($user->id);
});

test('task user relationship loads correctly', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    $task->load('user');

    expect($task->relationLoaded('user'))->toBeTrue();
    expect($task->user->id)->toBe($user->id);
});

<?php

use App\Models\Task;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('tasks.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit the tasks index page', function () {
    $user = User::factory()->create();
    Task::factory()->count(5)->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('tasks.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('tasks/index')
                ->has('tasks.data')
                ->has('tasks.links')
                ->where('showCompleted', false)
        );
});

test('tasks index page shows only active tasks by default', function () {
    $user = User::factory()->create();
    Task::factory()->count(3)->create([
        'user_id' => $user->id,
        'is_completed' => false,
    ]);
    Task::factory()->count(2)->create([
        'user_id' => $user->id,
        'is_completed' => true,
    ]);

    $this->actingAs($user)
        ->get(route('tasks.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('tasks/index')
                ->has('tasks.data', 3)
                ->where('showCompleted', false)
        );
});

test('tasks index page can show completed tasks', function () {
    $user = User::factory()->create();
    Task::factory()->count(2)->create([
        'user_id' => $user->id,
        'is_completed' => false,
    ]);
    Task::factory()->count(3)->create([
        'user_id' => $user->id,
        'is_completed' => true,
    ]);

    $this->actingAs($user)
        ->get(route('tasks.index', ['show_completed' => true]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('tasks/index')
                ->has('tasks.data', 3)
                ->where('showCompleted', true)
        );
});

test('users can only see their own tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Task::factory()->count(3)->create([
        'user_id' => $user1->id,
        'is_completed' => false,
    ]);
    Task::factory()->count(2)->create(['user_id' => $user2->id]);

    $this->actingAs($user1)
        ->get(route('tasks.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('tasks/index')
                ->has('tasks.data', 3)
        );
});

test('authenticated users can visit the create task page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('tasks.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('tasks/create'));
});

test('guests cannot create tasks', function () {
    $this->post(route('tasks.store'), [
        'description' => 'Test task',
    ])->assertRedirect(route('login'));
});

test('authenticated users can create a task', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [
            'description' => 'Test task description',
            'due_date' => '2024-12-31',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'user_id' => $user->id,
        'description' => 'Test task description',
        'due_date' => '2024-12-31 00:00:00',
        'is_completed' => 0,
    ]);
});

test('task creation requires description', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.store'), [])
        ->assertSessionHasErrors('description');
});

test('authenticated users can view their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'description' => 'Test task',
    ]);

    $this->actingAs($user)
        ->get(route('tasks.show', $task))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('tasks/show')
                ->where('task.id', $task->id)
                ->where('task.description', 'Test task')
        );
});

test('users cannot view other users tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);

    $this->actingAs($user1)
        ->get(route('tasks.show', $task))
        ->assertForbidden();
});

test('authenticated users can update their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'description' => 'Original description',
        'is_completed' => false,
    ]);

    $this->actingAs($user)
        ->put(route('tasks.update', $task), [
            'description' => 'Updated description',
            'due_date' => '2024-12-31',
            'is_completed' => true,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'description' => 'Updated description',
        'due_date' => '2024-12-31 00:00:00',
        'is_completed' => 1,
    ]);
});

test('users cannot update other users tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);

    $this->actingAs($user1)
        ->put(route('tasks.update', $task), [
            'description' => 'Hacked description',
        ])
        ->assertForbidden();
});

test('authenticated users can delete their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('tasks.destroy', $task))
        ->assertRedirect(route('tasks.index'));

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('users cannot delete other users tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);

    $this->actingAs($user1)
        ->delete(route('tasks.destroy', $task))
        ->assertForbidden();

    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});

test('authenticated users can add comments to their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'comments' => [],
    ]);

    $this->actingAs($user)
        ->post(route('tasks.comments.store', $task), [
            'comment' => 'This is a test comment',
        ])
        ->assertRedirect();

    $task->refresh();
    expect($task->comments)->toContain('This is a test comment');
});

test('users cannot add comments to other users tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);

    $this->actingAs($user1)
        ->post(route('tasks.comments.store', $task), [
            'comment' => 'Hacked comment',
        ])
        ->assertForbidden();
});

test('adding comment requires comment text', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('tasks.comments.store', $task), [])
        ->assertSessionHasErrors('comment');
});

test('authenticated users can update reminder for their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'next_reminder' => null,
    ]);

    $this->actingAs($user)
        ->put(route('tasks.reminder.update', $task), [
            'next_reminder' => '2024-12-31 10:00:00',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'next_reminder' => '2024-12-31 10:00:00',
    ]);
});

test('users can clear reminder for their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'next_reminder' => '2024-12-31 10:00:00',
    ]);

    $this->actingAs($user)
        ->put(route('tasks.reminder.update', $task), [
            'next_reminder' => null,
        ])
        ->assertRedirect();

    $task->refresh();
    expect($task->next_reminder)->toBeNull();
});

test('users cannot update reminder for other users tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);

    $this->actingAs($user1)
        ->put(route('tasks.reminder.update', $task), [
            'next_reminder' => '2024-12-31 10:00:00',
        ])
        ->assertForbidden();
});

test('tasks are ordered by created_at descending', function () {
    $user = User::factory()->create();

    $oldTask = Task::factory()->create([
        'user_id' => $user->id,
        'description' => 'Old Task',
        'is_completed' => false,
        'created_at' => now()->subDay(),
    ]);

    $newTask = Task::factory()->create([
        'user_id' => $user->id,
        'description' => 'New Task',
        'is_completed' => false,
        'created_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('tasks.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('tasks/index')
                ->where('tasks.data.0.description', 'New Task')
                ->where('tasks.data.1.description', 'Old Task')
        );
});

<?php

namespace App\Http\Controllers;

use App\Actions\AddCommentAction;
use App\Actions\CreateTaskAction;
use App\Actions\DeleteTaskAction;
use App\Actions\UpdateNextReminderAction;
use App\Actions\UpdateTaskAction;
use App\Http\Requests\AddCommentRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateReminderRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Queries\TaskQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, TaskQuery $taskQuery)
    {
        $showCompleted = $request->boolean('show_completed', false);

        $tasks = $taskQuery->forUser(
            auth()->id(),
            $showCompleted
        )->paginate(15);

        return Inertia::render('tasks/index', [
            'tasks' => $tasks,
            'showCompleted' => $showCompleted,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('tasks/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, CreateTaskAction $createTaskAction)
    {
        $task = $createTaskAction->execute(
            auth()->user(),
            $request->validated('description'),
            $request->validated('due_date')
        );

        Inertia::flash('notification', [
            'type' => 'success',
            'message' => 'Task created successfully.',
            'title' => 'Success',
        ]);

        return redirect()->route('tasks.show', $task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('tasks/show', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateTaskRequest $request,
        Task $task,
        UpdateTaskAction $updateTaskAction
    ) {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $updateTaskAction->execute(
            $task,
            $request->validated('description'),
            $request->validated('due_date'),
            $request->validated('is_completed')
        );

        return Inertia::flash('notification', [
            'type' => 'success',
            'message' => 'Task updated successfully.',
            'title' => 'Success',
        ])->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task, DeleteTaskAction $deleteTaskAction)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $deleteTaskAction->execute($task);

        Inertia::flash('notification', [
            'type' => 'success',
            'message' => 'Task deleted successfully.',
            'title' => 'Success',
        ]);

        return redirect()->route('tasks.index');
    }

    /**
     * Add a comment to the task.
     */
    public function addComment(
        AddCommentRequest $request,
        Task $task,
        AddCommentAction $addCommentAction
    ) {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $addCommentAction->execute($task, $request->validated('comment'));

        return Inertia::flash('notification', [
            'type' => 'success',
            'message' => 'Comment added successfully.',
            'title' => 'Success',
        ])->back();
    }

    /**
     * Update the next reminder for the task.
     */
    public function updateReminder(
        UpdateReminderRequest $request,
        Task $task,
        UpdateNextReminderAction $updateNextReminderAction
    ) {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $updateNextReminderAction->execute($task, $request->validated('next_reminder'));

        return Inertia::flash('notification', [
            'type' => 'success',
            'message' => 'Reminder updated successfully.',
            'title' => 'Success',
        ])->back();
    }
}

<?php

use Prosvirin\LaravelTodoInspector\Models\TodoTask;

it('can create a task', function () {
    $task = TodoTask::create([
        'file_path' => 'app/Http/Controllers/TestController.php',
        'line_number' => 10,
        'content' => 'Fix this bug',
        'type' => 'TODO',
        'priority' => 'HIGH',
        'hash' => md5('test'),
    ]);

    expect($task)->toBeInstanceOf(TodoTask::class);
    expect($task->file_path)->toBe('app/Http/Controllers/TestController.php');
    expect($task->content)->toBe('Fix this bug');
});

it('has default priority LOW', function () {
    $task = TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Test',
        'type' => 'TODO',
        'hash' => md5('test2'),
    ]);

    expect($task->priority)->toBe('LOW');
});

it('has scope for pending tasks', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Pending',
        'type' => 'TODO',
        'status' => TodoTask::STATUS_PENDING,
        'hash' => md5('pending'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'Done',
        'type' => 'TODO',
        'status' => TodoTask::STATUS_DONE,
        'hash' => md5('done'),
    ]);

    $pending = TodoTask::pending()->get();

    expect($pending)->toHaveCount(1);
    expect($pending->first()->content)->toBe('Pending');
});

it('has scope for active tasks', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Pending',
        'type' => 'TODO',
        'status' => TodoTask::STATUS_PENDING,
        'hash' => md5('pending'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'In Progress',
        'type' => 'TODO',
        'status' => TodoTask::STATUS_IN_PROGRESS,
        'hash' => md5('progress'),
    ]);

    $active = TodoTask::active()->get();

    expect($active)->toHaveCount(2);
});

it('has scope for completed tasks', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Done',
        'type' => 'TODO',
        'status' => TodoTask::STATUS_DONE,
        'hash' => md5('done'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'Wont Fix',
        'type' => 'TODO',
        'status' => TodoTask::STATUS_WONT_FIX,
        'hash' => md5('wontfix'),
    ]);

    $completed = TodoTask::completed()->get();

    expect($completed)->toHaveCount(2);
});

it('has scope by type', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'TODO task',
        'type' => 'TODO',
        'hash' => md5('todo'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'FIXME task',
        'type' => 'FIXME',
        'hash' => md5('fixme'),
    ]);

    $todos = TodoTask::ofType('TODO')->get();

    expect($todos)->toHaveCount(1);
    expect($todos->first()->type)->toBe('TODO');
});

it('has scope by priority', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'High priority',
        'type' => 'TODO',
        'priority' => 'HIGH',
        'hash' => md5('high'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'Low priority',
        'type' => 'TODO',
        'priority' => 'LOW',
        'hash' => md5('low'),
    ]);

    $high = TodoTask::withPriority('HIGH')->get();

    expect($high)->toHaveCount(1);
    expect($high->first()->priority)->toBe('HIGH');
});

it('has markAsDone method', function () {
    $task = TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Test',
        'type' => 'TODO',
        'hash' => md5('test'),
    ]);

    $task->markAsDone();

    expect($task->fresh()->status)->toBe(TodoTask::STATUS_DONE);
});

it('has assignTo method', function () {
    $task = TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Test',
        'type' => 'TODO',
        'hash' => md5('test'),
    ]);

    $task->assignTo('john');

    expect($task->fresh()->assigned_to)->toBe('john');
});

it('has isDone method', function () {
    $task = TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Test',
        'type' => 'TODO',
        'status' => TodoTask::STATUS_DONE,
        'hash' => md5('test'),
    ]);

    expect($task->isDone())->toBeTrue();
});

it('has short content attribute', function () {
    $longContent = str_repeat('a', 150);
    $task = TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => $longContent,
        'type' => 'TODO',
        'hash' => md5('test'),
    ]);

    expect(strlen($task->short_content))->toBeLessThanOrEqual(103);
});

it('has type icon attribute', function () {
    $task = TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Test',
        'type' => 'TODO',
        'hash' => md5('test'),
    ]);

    expect($task->type_icon)->toBe('📝');
});
<?php

use Prosvirin\LaravelTodoInspector\Services\TaskFilterService;
use Prosvirin\LaravelTodoInspector\Models\TodoTask;
use Prosvirin\LaravelTodoInspector\Http\Requests\TodoIndexRequest;

beforeEach(function () {
    TodoTask::truncate();
});

it('filters by type', function () {
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

    $request = new TodoIndexRequest();
    $request->query->set('type', 'TODO');

    $service = new TaskFilterService();
    $query = $service->applyFilters(TodoTask::query(), $request);

    expect($query->count())->toBe(1);
    expect($query->first()->type)->toBe('TODO');
});

it('filters by priority', function () {
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

    $request = new TodoIndexRequest();
    $request->query->set('priority', 'HIGH');

    $service = new TaskFilterService();
    $query = $service->applyFilters(TodoTask::query(), $request);

    expect($query->count())->toBe(1);
    expect($query->first()->priority)->toBe('HIGH');
});

it('filters by status', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Pending task',
        'type' => 'TODO',
        'status' => 'pending',
        'hash' => md5('pending'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'Done task',
        'type' => 'TODO',
        'status' => 'done',
        'hash' => md5('done'),
    ]);

    $request = new TodoIndexRequest();
    $request->query->set('status', 'pending');

    $service = new TaskFilterService();
    $query = $service->applyFilters(TodoTask::query(), $request);

    expect($query->count())->toBe(1);
    expect($query->first()->status)->toBe('pending');
});

it('searches by content', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Fix authentication bug',
        'type' => 'FIXME',
        'hash' => md5('auth'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'Add new feature',
        'type' => 'TODO',
        'hash' => md5('feature'),
    ]);

    $request = new TodoIndexRequest();
    $request->query->set('search', 'authentication');

    $service = new TaskFilterService();
    $query = $service->applyFilters(TodoTask::query(), $request);

    expect($query->count())->toBe(1);
    expect($query->first()->content)->toContain('authentication');
});
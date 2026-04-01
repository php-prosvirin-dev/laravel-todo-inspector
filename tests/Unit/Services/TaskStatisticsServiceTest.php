<?php

use Prosvirin\LaravelTodoInspector\Services\TaskStatisticsService;
use Prosvirin\LaravelTodoInspector\Models\TodoTask;

beforeEach(function () {
    TodoTask::truncate();
});

it('returns total tasks count', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Task 1',
        'type' => 'TODO',
        'hash' => md5('1'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'Task 2',
        'type' => 'FIXME',
        'hash' => md5('2'),
    ]);

    $service = new TaskStatisticsService();
    $stats = $service->getStatistics();

    expect($stats['total'])->toBe(2);
});

it('returns statistics by type', function () {
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

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 3,
        'content' => 'TODO task 2',
        'type' => 'TODO',
        'hash' => md5('todo2'),
    ]);

    $service = new TaskStatisticsService();
    $stats = $service->getStatistics();

    expect($stats['by_type'])->toHaveCount(2);
    expect($stats['by_type']->firstWhere('type', 'TODO')->count)->toBe(2);
    expect($stats['by_type']->firstWhere('type', 'FIXME')->count)->toBe(1);
});

it('returns statistics by priority', function () {
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
        'content' => 'Medium priority',
        'type' => 'TODO',
        'priority' => 'MEDIUM',
        'hash' => md5('medium'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 3,
        'content' => 'High priority 2',
        'type' => 'FIXME',
        'priority' => 'HIGH',
        'hash' => md5('high2'),
    ]);

    $service = new TaskStatisticsService();
    $stats = $service->getStatistics();

    expect($stats['by_priority'])->toHaveCount(2);
    expect($stats['by_priority']->firstWhere('priority', 'HIGH')->count)->toBe(2);
    expect($stats['by_priority']->firstWhere('priority', 'MEDIUM')->count)->toBe(1);
});

it('returns statistics by status', function () {
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
        'content' => 'In progress task',
        'type' => 'TODO',
        'status' => 'in_progress',
        'hash' => md5('progress'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 3,
        'content' => 'Done task',
        'type' => 'TODO',
        'status' => 'done',
        'hash' => md5('done'),
    ]);

    $service = new TaskStatisticsService();
    $stats = $service->getStatistics();

    expect($stats['by_status'])->toHaveCount(3);
    expect($stats['by_status']->firstWhere('status', 'pending')->count)->toBe(1);
    expect($stats['by_status']->firstWhere('status', 'in_progress')->count)->toBe(1);
    expect($stats['by_status']->firstWhere('status', 'done')->count)->toBe(1);
});

it('returns empty statistics when no tasks exist', function () {
    $service = new TaskStatisticsService();
    $stats = $service->getStatistics();

    expect($stats['total'])->toBe(0);
    expect($stats['by_type'])->toBeEmpty();
    expect($stats['by_priority'])->toBeEmpty();
    expect($stats['by_status'])->toBeEmpty();
});

it('orders types alphabetically', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'HACK task',
        'type' => 'HACK',
        'hash' => md5('hack'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'FIXME task',
        'type' => 'FIXME',
        'hash' => md5('fixme'),
    ]);

    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 3,
        'content' => 'TODO task',
        'type' => 'TODO',
        'hash' => md5('todo'),
    ]);

    $service = new TaskStatisticsService();
    $stats = $service->getStatistics();

    $types = $stats['by_type']->pluck('type')->toArray();
    expect($types)->toBe(['FIXME', 'HACK', 'TODO']);
});
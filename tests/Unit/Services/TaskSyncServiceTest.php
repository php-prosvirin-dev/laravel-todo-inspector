<?php

use Prosvirin\LaravelTodoInspector\Services\TaskSyncService;
use Prosvirin\LaravelTodoInspector\Classes\Scanner;
use Prosvirin\LaravelTodoInspector\Models\TodoTask;

it('syncs tasks successfully', function () {
    $scanner = mock(Scanner::class);
    $scanner->shouldReceive('scan')->once()->andReturn([
        [
            'hash' => md5('test'),
            'file_path' => 'test.php',
            'line_number' => 1,
            'content' => 'Test task',
            'type' => 'TODO',
            'priority' => 'MEDIUM',
            'author' => null,
        ],
    ]);

    $service = new TaskSyncService($scanner);
    $result = $service->sync();

    expect($result['count'])->toBe(1);
    expect($result['deleted'])->toBe(0);
    expect(TodoTask::count())->toBe(1);
});

it('removes obsolete tasks', function () {
    TodoTask::create([
        'file_path' => 'old.php',
        'line_number' => 1,
        'content' => 'Old task',
        'type' => 'TODO',
        'hash' => md5('old'),
    ]);

    $scanner = mock(Scanner::class);
    $scanner->shouldReceive('scan')->once()->andReturn([]);

    $service = new TaskSyncService($scanner);
    $result = $service->sync();

    expect($result['deleted'])->toBe(1);
    expect(TodoTask::count())->toBe(0);
});
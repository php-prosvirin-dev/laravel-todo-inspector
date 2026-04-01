<?php

use Prosvirin\LaravelTodoInspector\Models\TodoTask;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    TodoTask::truncate();
});

it('runs scan command successfully', function () {
    Artisan::call('todo:scan');

    $output = Artisan::output();
    expect($output)->toContain('Scanning project for todo comments');
    expect($output)->toContain('Scan completed');
});

it('clears tasks before scanning with --clear flag', function () {
    TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Existing task',
        'type' => 'TODO',
        'hash' => md5('existing'),
    ]);

    expect(TodoTask::count())->toBe(1);

    Artisan::call('todo:scan', ['--clear' => true]);

    $output = Artisan::output();
    expect($output)->toContain('Existing tasks cleared');
});

it('filters by type with --type flag', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($tempFile, '<?php // TODO: Test task');
    $originalBasePath = base_path();
    $this->app->instance('path.base', sys_get_temp_dir());

    Artisan::call('todo:scan', ['--type' => 'TODO']);

    $output = Artisan::output();
    expect($output)->toContain('Found:');

    $this->app->instance('path.base', $originalBasePath);
    unlink($tempFile);
});

it('filters by path with --path flag', function () {
    Artisan::call('todo:scan', ['--path' => 'app/Http']);

    $output = Artisan::output();
    expect($output)->toContain('Scanning project');
});
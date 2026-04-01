<?php

use Prosvirin\LaravelTodoInspector\Models\TodoTask;
use Prosvirin\LaravelTodoInspector\Classes\Scanner;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    // Mock auth
    $this->withoutMiddleware(\Prosvirin\LaravelTodoInspector\Http\Middleware\BasicAuthMiddleware::class);

    Route::get('/todo-inspector', function () {
        return redirect()->route('todo-inspector.index');
    })->name('todo-inspector.index');
});

it('scans and redirects with success message', function () {
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

    $this->app->instance(Scanner::class, $scanner);

    $response = $this->get('/todo-inspector/scan');

    $response->assertRedirect(route('todo-inspector.index'));
    $response->assertSessionHas('success', 'Scan completed! Found 1 new/updated tasks. Removed 0 obsolete tasks.');
});

it('shows message when no tasks found', function () {
    $scanner = mock(Scanner::class);
    $scanner->shouldReceive('scan')->once()->andReturn([]);

    $this->app->instance(Scanner::class, $scanner);

    $response = $this->get('/todo-inspector/scan');

    $response->assertRedirect(route('todo-inspector.index'));
    $response->assertSessionHas('success', 'No new tasks found. Everything is up to date!');
});

it('removes obsolete tasks during scan', function () {
    TodoTask::create([
        'file_path' => 'old.php',
        'line_number' => 1,
        'content' => 'Old task',
        'type' => 'TODO',
        'hash' => md5('old'),
    ]);

    $scanner = mock(Scanner::class);
    $scanner->shouldReceive('scan')->once()->andReturn([]);

    $this->app->instance(Scanner::class, $scanner);

    $response = $this->get('/todo-inspector/scan');

    expect(TodoTask::count())->toBe(0);
    $response->assertSessionHas('success', 'No new tasks found. Everything is up to date!');
});
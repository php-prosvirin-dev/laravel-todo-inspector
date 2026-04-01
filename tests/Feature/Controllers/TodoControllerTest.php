<?php

use Prosvirin\LaravelTodoInspector\Models\TodoTask;

beforeEach(function () {
    // Disable auth middleware for these tests
    $this->withoutMiddleware(\Prosvirin\LaravelTodoInspector\Http\Middleware\BasicAuthMiddleware::class);
});

it('returns index page', function () {
    $response = $this->get('/todo-inspector');

    $response->assertStatus(200);
    $response->assertViewIs('todo-inspector::index');
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

    $response = $this->get('/todo-inspector?type=TODO');

    $response->assertSee('TODO task');
    $response->assertDontSee('FIXME task');
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

    $response = $this->get('/todo-inspector?priority=HIGH');

    $response->assertSee('High priority');
    $response->assertDontSee('Low priority');
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

    $response = $this->get('/todo-inspector?status=pending');

    $response->assertSee('Pending task');
    $response->assertDontSee('Done task');
});

it('searches tasks', function () {
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

    $response = $this->get('/todo-inspector?search=authentication');

    $response->assertSee('Fix authentication bug');
    $response->assertDontSee('Add new feature');
});

it('updates task status', function () {
    $task = TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Test task',
        'type' => 'TODO',
        'hash' => md5('test'),
    ]);

    $response = $this->put("/todo-inspector/{$task->id}", [
        'status' => TodoTask::STATUS_DONE,
    ]);

    $response->assertRedirect();
    expect($task->fresh()->status)->toBe(TodoTask::STATUS_DONE);
});

it('bulk updates tasks', function () {
    $task1 = TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 1,
        'content' => 'Task 1',
        'type' => 'TODO',
        'hash' => md5('task1'),
    ]);

    $task2 = TodoTask::create([
        'file_path' => 'test.php',
        'line_number' => 2,
        'content' => 'Task 2',
        'type' => 'TODO',
        'hash' => md5('task2'),
    ]);

    $response = $this->post('/todo-inspector/bulk', [
        'ids' => [$task1->id, $task2->id],
        'status' => TodoTask::STATUS_DONE,
    ]);

    $response->assertRedirect();
    expect($task1->fresh()->status)->toBe(TodoTask::STATUS_DONE);
    expect($task2->fresh()->status)->toBe(TodoTask::STATUS_DONE);
});

it('validates bulk update input', function () {
    $response = $this->post('/todo-inspector/bulk', [
        'ids' => ['invalid'],
        'status' => 'invalid',
    ]);

    $response->assertSessionHasErrors(['ids.0', 'status']);
});

it('updates with language parameter', function () {
    $response = $this->get('/todo-inspector?lang=ru');

    $response->assertStatus(200);
    expect(session('locale'))->toBe('ru');
});
<?php

use Illuminate\Support\Facades\Route;
use Prosvirin\LaravelTodoInspector\Http\Controllers\ScanController;
use Prosvirin\LaravelTodoInspector\Http\Controllers\TodoController;
use Prosvirin\LaravelTodoInspector\Http\Middleware\BasicAuthMiddleware;

Route::middleware(['web', 'todo-inspector.locale', BasicAuthMiddleware::class])->prefix('todo-inspector')->group(function () {
    Route::get('/', [TodoController::class, 'index'])->name('todo-inspector.index');
    Route::put('/{task}', [TodoController::class, 'update'])->name('todo-inspector.update');
    Route::post('/bulk', [TodoController::class, 'bulkUpdate'])->name('todo-inspector.bulk');
    Route::get('/scan', [ScanController::class, 'scan'])->name('todo-inspector.scan');
});

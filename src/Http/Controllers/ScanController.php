<?php

namespace Prosvirin\LaravelTodoInspector\Http\Controllers;

use Illuminate\Routing\Controller;
use Prosvirin\LaravelTodoInspector\Http\Requests\TodoScanRequest;
use Prosvirin\LaravelTodoInspector\Services\TaskSyncService;

class ScanController extends Controller
{
    public function __construct(
        private TaskSyncService $syncService
    ) {}

    public function scan(TodoScanRequest $request)
    {
        $result = $this->syncService->sync();

        $message = $result['count'] === 0
            ? 'No new tasks found. Everything is up to date!'
            : "Scan completed! Found {$result['count']} new/updated tasks. Removed {$result['deleted']} obsolete tasks.";

        return redirect()->route('todo-inspector.index')->with('success', $message);
    }
}

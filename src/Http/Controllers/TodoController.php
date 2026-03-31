<?php

namespace Prosvirin\LaravelTodoInspector\Http\Controllers;

use Illuminate\Routing\Controller;
use Prosvirin\LaravelTodoInspector\Http\Requests\TodoBulkUpdateRequest;
use Prosvirin\LaravelTodoInspector\Http\Requests\TodoIndexRequest;
use Prosvirin\LaravelTodoInspector\Http\Requests\TodoUpdateRequest;
use Prosvirin\LaravelTodoInspector\Models\TodoTask;
use Prosvirin\LaravelTodoInspector\Services\LocaleService;
use Prosvirin\LaravelTodoInspector\Services\TaskFilterService;
use Prosvirin\LaravelTodoInspector\Services\TaskStatisticsService;

class TodoController extends Controller
{
    public function __construct(
        private LocaleService $localeService,
        private TaskFilterService $filterService,
        private TaskStatisticsService $statisticsService
    ) {}

    public function index(TodoIndexRequest $request)
    {
        $this->localeService->setLocale($request);

        $query = $this->filterService->applyFilters(TodoTask::query(), $request);
        $tasks = $query->paginate(20)->withQueryString();

        $stats = $this->statisticsService->getStatistics();

        return view('todo-inspector::index', [
            'tasks' => $tasks,
            'stats' => $stats,
            'types' => ['TODO', 'FIXME', 'HACK', 'REVIEW', 'NOTE'],
            'priorities' => TodoTask::PRIORITIES,
            'statuses' => TodoTask::STATUSES,
        ]);
    }

    public function update(TodoUpdateRequest $request, TodoTask $task)
    {
        $task->update($request->validated());

        return redirect()->back()->with('success', 'Task updated successfully');
    }

    public function bulkUpdate(TodoBulkUpdateRequest $request)
    {
        TodoTask::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->back()->with('success', count($request->ids).' tasks updated');
    }
}

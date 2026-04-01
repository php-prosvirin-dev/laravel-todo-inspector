<?php

namespace Prosvirin\LaravelTodoInspector\Services;

use Prosvirin\LaravelTodoInspector\Models\TodoTask;

class TaskStatisticsService
{
    public function getStatistics(): array
    {
        return [
            'total' => TodoTask::count(),
            'by_type' => TodoTask::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->orderBy('type')
                ->get(),
            'by_priority' => TodoTask::selectRaw('priority, count(*) as count')
                ->groupBy('priority')
                ->get(),
            'by_status' => TodoTask::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get(),
        ];
    }
}

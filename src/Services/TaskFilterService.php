<?php

namespace Prosvirin\LaravelTodoInspector\Services;

use Illuminate\Database\Eloquent\Builder;
use Prosvirin\LaravelTodoInspector\Http\Requests\TodoIndexRequest;

class TaskFilterService
{
    public function applyFilters(Builder $query, TodoIndexRequest $request): Builder
    {
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', '%'.$search.'%')
                    ->orWhere('file_path', 'like', '%'.$search.'%')
                    ->orWhere('author', 'like', '%'.$search.'%');
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        return $query;
    }
}

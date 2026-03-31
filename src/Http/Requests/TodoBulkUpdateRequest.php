<?php

namespace Prosvirin\LaravelTodoInspector\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Prosvirin\LaravelTodoInspector\Models\TodoTask;

class TodoBulkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tableName = config('todo-inspector.table_name', 'todo_inspector_tasks');

        return [
            'ids' => 'required|array',
            'ids.*' => "exists:{$tableName},id",
            'status' => 'required|string|in:'.implode(',', array_keys(TodoTask::STATUSES)),
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Please select at least one task.',
            'ids.array' => 'Invalid task selection format.',
            'ids.*.exists' => 'One or more selected tasks do not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status value.',
        ];
    }
}

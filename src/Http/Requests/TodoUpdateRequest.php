<?php

namespace Prosvirin\LaravelTodoInspector\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Prosvirin\LaravelTodoInspector\Models\TodoTask;

class TodoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:'.implode(',', array_keys(TodoTask::STATUSES)),
            'priority' => 'nullable|string|in:'.implode(',', array_keys(TodoTask::PRIORITIES)),
            'assigned_to' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status value.',
            'priority.in' => 'Invalid priority value.',
            'assigned_to.max' => 'Assigned user name is too long.',
        ];
    }
}

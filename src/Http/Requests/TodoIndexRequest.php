<?php

namespace Prosvirin\LaravelTodoInspector\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $availableLocales = array_keys(config('todo-inspector.locales', ['en', 'ru', 'uk']));

        return [
            'type' => 'nullable|string|in:TODO,FIXME,HACK,REVIEW,NOTE',
            'priority' => 'nullable|string|in:LOW,MEDIUM,HIGH,CRITICAL',
            'status' => 'nullable|string|in:pending,in_progress,done,wont_fix',
            'search' => 'nullable|string|max:255',
            'sort' => 'nullable|string|in:created_at,updated_at,file_path,type,priority,status',
            'direction' => 'nullable|string|in:asc,desc',
            'lang' => 'nullable|string|in:'.implode(',', $availableLocales),
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Invalid task type specified.',
            'priority.in' => 'Invalid priority level specified.',
            'status.in' => 'Invalid status specified.',
            'sort.in' => 'Invalid sort field specified.',
            'direction.in' => 'Invalid sort direction specified.',
            'lang.in' => 'Invalid language specified.',
        ];
    }
}

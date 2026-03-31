<div class="overflow-x-auto">
    <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg">
        <thead>
        <tr class="bg-gray-100 dark:bg-gray-700">
            <th class="px-4 py-3 w-10"><input type="checkbox" id="select-all" class="rounded dark:bg-gray-600"></th>
            <th class="px-4 py-3 text-left dark:text-white">{{ __('tasks.type') }}</th>
            <th class="px-4 py-3 text-left dark:text-white">{{ __('tasks.content') }}</th>
            <th class="px-4 py-3 text-left dark:text-white">{{ __('tasks.file_line') }}</th>
            <th class="px-4 py-3 text-left dark:text-white">{{ __('tasks.priority') }}</th>
            <th class="px-4 py-3 text-left dark:text-white">{{ __('tasks.status') }}</th>
            <th class="px-4 py-3 text-left dark:text-white">{{ __('tasks.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($tasks as $task)
            <tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 text-center">
                    <input type="checkbox" class="task-checkbox rounded dark:bg-gray-600" value="{{ $task->id }}">
                </td>
                <td class="px-4 py-3">
                    <span class="badge badge-{{ strtolower($task->type) }}">
                        {{ $task->type_icon }} {{ $task->type }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm font-medium dark:text-white">{{ $task->short_content }}</div>
                </td>
                <td class="px-4 py-3">
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded dark:text-gray-300 block">
                        {{ $task->file_path }}:{{ $task->line_number }}
                    </code>
                </td>
                <td class="px-4 py-3">
                    <span class="badge badge-{{ strtolower($task->priority) }}">
                        {{ $task->priority }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <form action="{{ route('todo-inspector.update', $task) }}" method="POST" class="status-form">
                        @csrf
                        @method('PUT')
                        <select name="status" class="status-select text-sm border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ $task->status == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ $task->file_link }}" target="_blank"
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                       title="Open in PhpStorm">
                        <i class="fas fa-code fa-lg"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-check-circle text-4xl mb-2 block"></i>
                    <p>{!! __('tasks.no_tasks', ['command' => '<code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">php artisan todo:scan</code>']) !!}</p>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $tasks->links() }}
</div>

@if($tasks->total() > 0)
    <div class="pagination-info">
        Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} results
    </div>
@endif
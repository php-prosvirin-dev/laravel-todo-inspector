<div class="mb-4 flex gap-2">
    <x-todo-inspector::button onclick="window.bulkUpdate('done')" variant="success" icon="check">
        {{ __('tasks.mark_done') }}
    </x-todo-inspector::button>
    <x-todo-inspector::button onclick="window.bulkUpdate('in_progress')" variant="primary" icon="play">
        {{ __('tasks.mark_progress') }}
    </x-todo-inspector::button>
</div>
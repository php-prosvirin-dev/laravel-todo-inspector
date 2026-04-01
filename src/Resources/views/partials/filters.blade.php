<form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
    <input type="text" name="search" placeholder="{{ __('tasks.search') }}" value="{{ request('search') }}"
           class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

    <x-todo-inspector::select name="type"
                              :options="collect($types)->mapWithKeys(fn($t) => [$t => $t])->toArray()"
                              :selected="request('type')"
                              :placeholder="__('tasks.all_types')" />

    <x-todo-inspector::select name="priority"
                              :options="$priorities"
                              :selected="request('priority')"
                              :placeholder="__('tasks.all_priorities')" />

    <x-todo-inspector::select name="status"
                              :options="$statuses"
                              :selected="request('status')"
                              :placeholder="__('tasks.all_statuses')" />

    <div class="flex gap-2">
        <x-todo-inspector::button type="submit" variant="primary" icon="filter">
            {{ __('tasks.filter') }}
        </x-todo-inspector::button>
        <x-todo-inspector::button href="{{ route('todo-inspector.index') }}" variant="secondary" icon="times" />
    </div>
</form>
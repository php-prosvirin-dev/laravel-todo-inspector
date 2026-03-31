<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <x-todo-inspector::stat-card
            label="{{ __('tasks.total_tasks') }}"
            :value="$stats['total']"
            icon="📊"
            color="blue"
            :href="route('todo-inspector.index', array_merge(request()->except(['type', 'page']), ['type' => '']))" />

    @foreach($stats['by_type'] as $item)
        <x-todo-inspector::stat-card
                :label="$item->type"
                :value="$item->count"
                :icon="match($item->type) { 'TODO' => '📝', 'FIXME' => '🐛', 'HACK' => '🔧', 'REVIEW' => '👀', 'NOTE' => '📌' }"
                :badge="strtolower($item->type)"
                :href="route('todo-inspector.index', array_merge(request()->except(['type', 'page']), ['type' => $item->type]))" />
    @endforeach
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    @foreach($stats['by_priority'] as $item)
        @php
            $color = match($item->priority) {
                'CRITICAL' => 'red',
                'HIGH' => 'orange',
                'MEDIUM' => 'blue',
                'LOW' => 'green',
                default => 'gray',
            };
        @endphp
        <x-todo-inspector::stat-card
                :label="$item->priority"
                :value="$item->count"
                :color="$color"
                :badge="strtolower($item->priority)"
                :href="route('todo-inspector.index', array_merge(request()->except(['priority', 'page']), ['priority' => $item->priority]))" />
    @endforeach
</div>
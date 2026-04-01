<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ config('todo-inspector.theme', 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TODO Inspector - @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/todo-inspector/css/app.css') }}">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .theme-transition { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        a { text-decoration: none; }
    </style>

    @stack('styles')
</head>
<body class="{{ $isDark ?? false ? 'dark' : '' }} theme-transition">
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            @include('todo-inspector::partials.header')

            @if(session('success'))
                <div class="mx-6 mt-6">
                    <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded-lg">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <div class="p-6">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<div id="todo-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden" style="display: none !important;">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="todo-modal-content">
        <div class="px-6 py-4 max-h-[70vh] overflow-y-auto" id="todo-modal-body">
            <p style="max-height: 300px;overflow-y: auto;" class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words font-mono text-sm" id="todo-modal-text"></p>
        </div>
    </div>
</div>

<script>
    window.tasksTranslations = {
        select_task: '{{ __('tasks.select_task') }}',
        done: '{{ __('tasks.done') }}',
        in_progress: '{{ __('tasks.in_progress') }}',
        confirm_update: '{{ __('tasks.confirm_update') }}',
        confirm_logout: '{{ __('tasks.confirm_logout') }}'
    };
    window.bulkActionUrl = '{{ route('todo-inspector.bulk') }}';
</script>
<script src="{{ asset('vendor/todo-inspector/js/todo-inspector.js') }}"></script>
@stack('scripts')
</body>
</html>
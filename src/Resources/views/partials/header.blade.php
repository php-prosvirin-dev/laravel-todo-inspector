@php
    $currentLocale = app()->getLocale();
    $savedTheme = request()->cookie('theme');
    $configTheme = config('todo-inspector.theme', 'dark');
    $isDark = $configTheme === 'dark' || ($configTheme === 'auto' && $savedTheme === 'dark');

    $currentLocale = app()->getLocale();
    $locales = [
        'en' => ['flag' => '🇬🇧', 'name' => 'English'],
        'ru' => ['flag' => '🇷🇺', 'name' => 'Русский'],
        'uk' => ['flag' => '🇺🇦', 'name' => 'Українська'],
        'pl' => ['flag' => '🇵🇱', 'name' => 'Polski'],
        'de' => ['flag' => '🇩🇪', 'name' => 'Deutsch'],
        'fr' => ['flag' => '🇫🇷', 'name' => 'Français'],
        'es' => ['flag' => '🇪🇸', 'name' => 'Español'],
        'zh' => ['flag' => '🇨🇳', 'name' => '中文'],
        'ja' => ['flag' => '🇯🇵', 'name' => '日本語'],
    ];

@endphp

<div class="bg-blue-600 dark:bg-blue-700 px-6 py-4 flex justify-between items-center flex-wrap gap-4">
    <h1 class="text-2xl font-bold text-white">📋 {{ __('tasks.title') }}</h1>

    <div class="flex gap-2 items-center">
        <select id="lang-select" class="lang-select">
            @foreach($locales as $code => $locale)
                <option value="{{ $code }}" {{ $currentLocale === $code ? 'selected' : '' }}>
                    {{ $locale['flag'] }} {{ $locale['name'] }}
                </option>
            @endforeach
        </select>

        <button onclick="window.toggleTheme()" class="bg-white/20 text-white px-3 py-2 rounded-lg hover:bg-white/30 transition">
            <i id="theme-icon" class="fas {{ $isDark ? 'fa-sun' : 'fa-moon' }}"></i>
        </button>

        <a href="{{ route('todo-inspector.scan') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition">
            <i class="fas fa-sync-alt mr-2"></i> {{ __('tasks.scan_now') }}
        </a>
    </div>
</div>
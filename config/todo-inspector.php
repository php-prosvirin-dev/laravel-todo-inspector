<?php

return [
    'table_name' => env('TODO_INSPECTOR_TABLE', 'todo_inspector_tasks'),

    'theme' => env('TODO_INSPECTOR_THEME', 'dark'),

    'auth' => [
        'login' => env('TODO_INSPECTOR_LOGIN', 'admin'),
        'password' => env('TODO_INSPECTOR_PASSWORD', 'password'),
    ],

    'locales' => [
        'en' => 'English',
        'ru' => 'Русский',
        'uk' => 'Українська',
        'pl' => 'Polski',
        'de' => 'Deutsch',
        'fr' => 'Français',
        'es' => 'Español',
        'zh' => '中文',
        'ja' => '日本語',
    ],

    'extensions' => [
        'php',
        'js',
        'vue',
        'blade.php',
        'css',
        'scss',
    ],

    'exclude_dirs' => [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
        '.git',
        'tests',
    ],

    'exclude_files' => [
        'test-scan.php',
        'test-model.php',
        'test-*.php',
        '*_test.php',
        '*.test.php',
    ],

    'patterns' => [
        'TODO' => '/\/\/\s*TODO\s*:?\s*(?:\[([^\]]+)\])?\s*(.*)/i',
        'FIXME' => '/\/\/\s*FIXME\s*:?\s*(?:\[([^\]]+)\])?\s*(?:@([a-zA-Z0-9_-]+))?\s*(.*)/i',
        'HACK' => '/\/\/\s*HACK\s*:?\s*(?:\[([^\]]+)\])?\s*(.*)/i',
        'REVIEW' => '/\/\/\s*REVIEW\s*:?\s*(?:\[([^\]]+)\])?\s*(?:@([a-zA-Z0-9_-]+))?\s*(.*)/i',
        'NOTE' => '/\/\/\s*NOTE\s*:?\s*(.*)/i',
    ],

    'doc_patterns' => [
        'TODO' => '/TODO\s*:?\s*(?:\[([^\]]+)\])?\s*(.*)/i',
        'FIXME' => '/FIXME\s*:?\s*(?:\[([^\]]+)\])?\s*(?:@([a-zA-Z0-9_-]+))?\s*(.*)/i',
        'HACK' => '/HACK\s*:?\s*(?:\[([^\]]+)\])?\s*(.*)/i',
        'REVIEW' => '/REVIEW\s*:?\s*(?:\[([^\]]+)\])?\s*(?:@([a-zA-Z0-9_-]+))?\s*(.*)/i',
        'NOTE' => '/NOTE\s*:?\s*(.*)/i',
    ],

    'github_repo' => env('TODO_INSPECTOR_GITHUB_REPO', null),
];

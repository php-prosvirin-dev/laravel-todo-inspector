<?php

return [
    'table_name' => env('TODO_INSPECTOR_TABLE', 'todo_inspector_tasks'),

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
        'test-simple.php',
        '*_test.php',
        '*.test.php',
        'Test.php',
    ],

    'patterns' => [
        'TODO' => '/\/\/\s*TODO\s*:?\s*(?:\[([^\]]+)\])?\s*(.*)/i',
        'FIXME' => '/\/\/\s*FIXME\s*:?\s*(?:\[([^\]]+)\])?\s*(?:@([a-zA-Z0-9_-]+))?\s*(.*)/i',
        'HACK' => '/\/\/\s*HACK\s*:?\s*(?:\[([^\]]+)\])?\s*(.*)/i',
        'REVIEW' => '/\/\/\s*REVIEW\s*:?\s*(?:\[([^\]]+)\])?\s*(?:@([a-zA-Z0-9_-]+))?\s*(.*)/i',
        'NOTE' => '/\/\/\s*NOTE\s*:?\s*(.*)/i',
    ],

    'github_repo' => env('TODO_INSPECTOR_GITHUB_REPO', null),
];

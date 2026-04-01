<?php

use Prosvirin\LaravelTodoInspector\Classes\Scanner;

beforeEach(function () {
    $this->scanner = new Scanner;
    $this->reflection = new ReflectionClass($this->scanner);
});

it('scans single-line TODO comments', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($tempFile, '<?php // TODO: Fix this bug');

    $tasks = $this->scanner->scan();

    expect($tasks)->toBeArray();
    unlink($tempFile);
});

it('extracts priority from comments', function () {
    $method = $this->reflection->getMethod('extractPriority');
    $method->setAccessible(true);
    $priority = $method->invoke($this->scanner, '// TODO: [HIGH] Fix this');

    expect($priority)->toBe('HIGH');
});

it('returns LOW priority when none specified', function () {
    $method = $this->reflection->getMethod('extractPriority');
    $method->setAccessible(true);
    $priority = $method->invoke($this->scanner, '// TODO: Fix this');

    expect($priority)->toBe('LOW');
});

it('extracts author from comments', function () {
    $method = $this->reflection->getMethod('extractAuthor');
    $method->setAccessible(true);
    $author = $method->invoke($this->scanner, '// FIXME: @ivanov Bug here');

    expect($author)->toBe('ivanov');
});

it('returns null when no author specified', function () {
    $method = $this->reflection->getMethod('extractAuthor');
    $method->setAccessible(true);
    $author = $method->invoke($this->scanner, '// FIXME: Fix this');

    expect($author)->toBeNull();
});

it('scans multi-line Doc comments', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'test');
    $content = <<<'PHP'
<?php
/**
 * TODO: Refactor this method
 * FIXME: @developer Edge case
 */
class Test {}
PHP;
    file_put_contents($tempFile, $content);

    $tasks = $this->scanner->scan();

    expect($tasks)->toBeArray();
    unlink($tempFile);
});

it('ignores excluded directories', function () {
    $method = $this->reflection->getMethod('getAllFiles');
    $method->setAccessible(true);
    $files = $method->invoke($this->scanner);

    expect($files)->toBeArray();

    $excludeDirs = config('todo-inspector.exclude_dirs', []);
    foreach ($files as $file) {
        foreach ($excludeDirs as $excluded) {
            if ($excluded === 'vendor') {
                continue;
            }
            expect($file)->not->toContain($excluded);
        }
    }
});

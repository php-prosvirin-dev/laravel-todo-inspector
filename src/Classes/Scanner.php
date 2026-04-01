<?php

namespace Prosvirin\LaravelTodoInspector\Classes;

use Illuminate\Support\Facades\File;

class Scanner
{
    protected array $patterns;

    protected array $excludeDirs;

    protected array $extensions;

    protected array $docPatterns;

    public function __construct()
    {
        $configPatterns = config('todo-inspector.patterns', [
            'TODO' => '/\/\/\s*TODO(?:\s*\(([^)]+)\))?\s*:?\s*(.+)/i',
            'FIXME' => '/\/\/\s*FIXME(?:\s*\(([^)]+)\))?\s*:?\s*(.+)/i',
            'HACK' => '/\/\/\s*HACK(?:\s*\(([^)]+)\))?\s*:?\s*(.+)/i',
            'REVIEW' => '/\/\/\s*REVIEW(?:\s*\(([^)]+)\))?\s*:?\s*(.+)/i',
            'NOTE' => '/\/\/\s*NOTE(?:\s*\(([^)]+)\))?\s*:?\s*(.+)/i',
        ]);

        $this->docPatterns = config('todo-inspector.doc-patterns', [
            'TODO' => '/TODO\s*:?\s*(?:\[([^\]]+)\])?\s*(.*)/i',
            'FIXME' => '/FIXME\s*:?\s*(?:\[([^\]]+)\])?\s*(?:@([a-zA-Z0-9_-]+))?\s*(.*)/i',
            'HACK' => '/HACK\s*:?\s*(?:\[([^\]]+)\])?\s*(.*)/i',
            'REVIEW' => '/REVIEW\s*:?\s*(?:\[([^\]]+)\])?\s*(?:@([a-zA-Z0-9_-]+))?\s*(.*)/i',
            'NOTE' => '/NOTE\s*:?\s*(.*)/i',
        ]);

        $this->patterns = [];
        foreach ($configPatterns as $type => $pattern) {
            if (is_array($pattern) && isset($pattern['pattern'])) {
                $this->patterns[$type] = $pattern['pattern'];
            } elseif (is_string($pattern)) {
                $this->patterns[$type] = $pattern;
            }
        }

        $this->excludeDirs = config('todo-inspector.exclude_dirs', [
            'vendor', 'node_modules', 'storage', 'bootstrap/cache', '.git',
        ]);

        $this->extensions = config('todo-inspector.extensions', [
            'php', 'js', 'vue', 'blade.php',
        ]);
    }

    public function scan(): array
    {
        $tasks = [];
        $files = $this->getAllFiles();

        foreach ($files as $file) {
            $fileTasks = $this->scanFile($file);
            $tasks = array_merge($tasks, $fileTasks);
        }

        return $tasks;
    }

    protected function getAllFiles(): array
    {
        $files = [];
        $basePath = base_path();
        $excludeFiles = config('todo-inspector.exclude_files', []);

        $directoryIterator = new \RecursiveDirectoryIterator($basePath);
        $iterator = new \RecursiveIteratorIterator($directoryIterator);

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            $filePath = $file->getPathname();
            $extension = $file->getExtension();

            $extensionMatched = false;
            foreach ($this->extensions as $ext) {
                if ($extension === $ext || ($ext === 'blade.php' && $file->getFilename() === $ext)) {
                    $extensionMatched = true;
                    break;
                }
            }

            if (! $extensionMatched) {
                continue;
            }

            if (! $this->isExcludedFile($filePath, $excludeFiles)) {
                $files[] = $filePath;
            }
        }

        return array_values($files);
    }

    protected function isExcludedFile(string $filePath, array $excludeFiles): bool
    {
        $fileName = basename($filePath);
        $relativePath = $this->getRelativePath($filePath);

        foreach ($this->excludeDirs as $excluded) {
            if (str_contains($relativePath, $excluded.'/') ||
                str_contains($relativePath, $excluded.DIRECTORY_SEPARATOR) ||
                str_starts_with($relativePath, $excluded.'/')) {
                return true;
            }
        }

        foreach ($excludeFiles as $pattern) {
            $pattern = trim($pattern);
            $regex = '/^'.str_replace('*', '.*', preg_quote($pattern, '/')).'$/i';

            if (preg_match($regex, $fileName)) {
                return true;
            }

            if (str_contains($fileName, $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function scanFile(string $filePath): array
    {
        $tasks = [];

        if (! File::exists($filePath)) {
            return $tasks;
        }

        $content = File::get($filePath);
        $lines = explode("\n", $content);
        $relativePath = $this->getRelativePath($filePath);

        foreach ($lines as $lineNumber => $line) {
            foreach ($this->patterns as $type => $pattern) {
                if (preg_match($pattern, $line, $matches)) {
                    $task = $this->extractTask($type, $line, $matches, $relativePath, $lineNumber);
                    if ($task) {
                        $tasks[] = $task;
                    }
                }
            }
        }

        $this->scanDocComments($content, $relativePath, $tasks);

        return $tasks;
    }

    protected function scanDocComments(string $content, string $relativePath, array &$tasks): void
    {
        $pattern = '/\/\*\*(.*?)\*\//s';

        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[1] as $match) {
                $commentContent = $match[0];
                $offset = $match[1];
                $lineNumber = substr_count(substr($content, 0, $offset), "\n");
                $commentLines = explode("\n", $commentContent);

                foreach ($commentLines as $commentLine) {
                    $cleanLine = preg_replace('/^\s*\*\s?/', '', $commentLine);

                    foreach ($this->docPatterns as $type => $docPattern) {
                        if (preg_match($docPattern, $cleanLine, $lineMatches)) {
                            $taskData = $this->extractTaskFromDoc($type, $cleanLine, $lineMatches, $relativePath, $lineNumber);
                            if ($taskData) {
                                $tasks[] = $taskData;
                            }
                        }
                    }
                }
            }
        }
    }

    protected function extractTaskFromDoc(string $type, string $line, array $matches, string $filePath, int $lineNumber): ?array
    {
        $content = '';
        $priority = null;
        $author = null;

        switch ($type) {
            case 'TODO':
                $priority = isset($matches[1]) ? strtoupper($matches[1]) : null;
                $content = trim($matches[2] ?? $matches[1] ?? $matches[0] ?? '');
                break;
            case 'FIXME':
            case 'REVIEW':
                $priority = isset($matches[1]) ? strtoupper($matches[1]) : null;
                $author = $matches[2] ?? null;
                $content = trim($matches[3] ?? $matches[0] ?? '');
                break;
            case 'HACK':
                $priority = isset($matches[1]) ? strtoupper($matches[1]) : null;
                $content = trim($matches[2] ?? $matches[0] ?? '');
                break;
            case 'NOTE':
                $content = trim($matches[1] ?? $matches[0] ?? '');
                break;
            default:
                $content = trim($matches[1] ?? $matches[0] ?? '');
                break;
        }

        $content = preg_replace('/^\s*\*\s*/', '', $content);
        $content = preg_replace('/\/\/\s*'.preg_quote($type, '/').'\s*:?\s*/i', '', $content);
        $content = trim($content);

        if (empty($content)) {
            return null;
        }

        if (! $priority) {
            $priority = $this->extractPriority($line);
        }

        if (! $author) {
            $author = $this->extractAuthor($line);
        }

        return [
            'file_path' => $filePath,
            'line_number' => $lineNumber + 1,
            'content' => $content,
            'type' => $type,
            'priority' => $priority ?? 'LOW',
            'author' => $author,
            'hash' => md5($filePath.$lineNumber.$line),
        ];
    }

    protected function extractTask(string $type, string $line, array $matches, string $filePath, int $lineNumber): ?array
    {
        $content = '';
        $priority = null;
        $author = null;

        switch ($type) {
            case 'TODO':
                $priority = isset($matches[1]) ? strtoupper($matches[1]) : null;
                $content = trim($matches[2] ?? $matches[0] ?? '');
                break;
            case 'FIXME':
            case 'REVIEW':
                $priority = isset($matches[1]) ? strtoupper($matches[1]) : null;
                $author = $matches[2] ?? null;
                $content = trim($matches[3] ?? $matches[0] ?? '');
                break;
            case 'HACK':
                $priority = isset($matches[1]) ? strtoupper($matches[1]) : null;
                $content = trim($matches[2] ?? $matches[0] ?? '');
                break;
            case 'NOTE':
                $content = trim($matches[1] ?? $matches[0] ?? '');
                break;
            default:
                $content = trim($matches[1] ?? $matches[0] ?? '');
                break;
        }

        $content = preg_replace('/^\/\/\s*/', '', $content);
        $content = preg_replace('/\s*'.preg_quote($type, '/').'\s*:?\s*/i', '', $content);
        $content = trim($content);
        $content = mb_substr($content, 0, 1000);

        if (empty($content)) {
            return null;
        }

        if (! $priority) {
            $priority = $this->extractPriority($line);
        }

        if (! $author) {
            $author = $this->extractAuthor($line);
        }

        return [
            'file_path' => $filePath,
            'line_number' => $lineNumber + 1,
            'content' => $content,
            'type' => $type,
            'priority' => $priority ?? 'LOW',
            'author' => $author,
            'hash' => md5($filePath.$lineNumber.$line),
        ];
    }

    protected function extractPriority(string $line): string
    {
        if (preg_match('/\[(LOW|MEDIUM|HIGH|CRITICAL)\]/i', $line, $matches)) {
            return strtoupper($matches[1]);
        }

        return 'LOW';
    }

    protected function extractAuthor(string $line): ?string
    {
        if (preg_match('/@([a-zA-Z0-9_-]+)/', $line, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function getRelativePath(string $filePath): string
    {
        $basePath = base_path();
        $relativePath = str_replace($basePath, '', $filePath);

        return ltrim($relativePath, DIRECTORY_SEPARATOR);
    }
}

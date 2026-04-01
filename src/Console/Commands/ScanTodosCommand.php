<?php

namespace Prosvirin\LaravelTodoInspector\Console\Commands;

use Illuminate\Console\Command;
use Prosvirin\LaravelTodoInspector\Classes\Scanner;
use Prosvirin\LaravelTodoInspector\Models\TodoTask;

class ScanTodosCommand extends Command
{
    protected $signature = 'todo:scan 
                            {--clear : Clear existing tasks before scanning}
                            {--type= : Scan only specific type (TODO, FIXME, HACK, REVIEW, NOTE)}
                            {--path= : Scan only specific directory}';

    protected $description = 'Scan project for TODO, FIXME, HACK, REVIEW, NOTE comments';

    public function handle(Scanner $scanner): int
    {
        $this->info('🔍 Scanning project for todo comments...');
        $this->line('📊 Using table: '.config('todo-inspector.table_name', 'todo_inspector_tasks'));

        $startTime = microtime(true);

        if ($this->option('clear')) {
            TodoTask::truncate();
            $this->info('✓ Existing tasks cleared');
        }

        $tasks = $scanner->scan();
        $count = 0;
        $skipped = 0;
        $skippedByType = [];

        foreach ($tasks as $taskData) {
            if ($this->option('type') && $taskData['type'] !== $this->option('type')) {
                $skipped++;
                $skippedByType[$taskData['type']] = ($skippedByType[$taskData['type']] ?? 0) + 1;

                continue;
            }

            if ($this->option('path') && ! str_contains($taskData['file_path'], $this->option('path'))) {
                $skipped++;

                continue;
            }

            TodoTask::updateOrCreate(
                ['hash' => $taskData['hash']],
                $taskData
            );
            $count++;
        }

        $existingHashes = array_column($tasks, 'hash');
        $deleted = TodoTask::whereNotIn('hash', $existingHashes)->delete();

        $time = round((microtime(true) - $startTime) * 1000, 2);

        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✓ Scan completed in {$time}ms");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("📝 Found: {$count} new/updated tasks");

        if ($skipped > 0) {
            $this->warn("⏭️  Skipped: {$skipped} tasks (filters applied)");
            if (! empty($skippedByType)) {
                foreach ($skippedByType as $type => $amount) {
                    $this->line("    └─ {$type}: {$amount}");
                }
            }
        }

        if ($deleted > 0) {
            $this->warn("🗑  Removed: {$deleted} obsolete tasks");
        }

        $this->showStatistics();

        return Command::SUCCESS;
    }

    protected function showStatistics(): void
    {
        $stats = TodoTask::selectRaw('type, priority, count(*) as count')
            ->groupBy('type', 'priority')
            ->get();

        if ($stats->count() > 0) {
            $this->newLine();
            $this->line('📊 Current statistics:');

            $byType = $stats->groupBy('type');
            $typeOrder = ['TODO', 'FIXME', 'HACK', 'REVIEW', 'NOTE'];

            foreach ($typeOrder as $type) {
                if (isset($byType[$type])) {
                    $items = $byType[$type];
                    $total = $items->sum('count');
                    $icon = $this->getTypeIcon($type);

                    $this->line("  {$icon} {$type}: {$total}");

                    foreach ($items as $item) {
                        $priorityIcon = $this->getPriorityIcon($item->priority);
                        $this->line("    └─ {$priorityIcon} {$item->priority}: {$item->count}");
                    }
                }
            }
        }
    }

    protected function getTypeIcon(string $type): string
    {
        return match ($type) {
            'TODO' => '📝',
            'FIXME' => '🐛',
            'HACK' => '🔧',
            'REVIEW' => '👀',
            'NOTE' => '📌',
            default => '📄',
        };
    }

    protected function getPriorityIcon(string $priority): string
    {
        return match ($priority) {
            'CRITICAL' => '🔴',
            'HIGH' => '🟠',
            'MEDIUM' => '🔵',
            'LOW' => '🟢',
            default => '⚪',
        };
    }
}

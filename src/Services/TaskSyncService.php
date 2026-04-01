<?php

namespace Prosvirin\LaravelTodoInspector\Services;

use Prosvirin\LaravelTodoInspector\Classes\Scanner;
use Prosvirin\LaravelTodoInspector\Models\TodoTask;

class TaskSyncService
{
    public function __construct(
        private Scanner $scanner
    ) {}

    public function sync(): array
    {
        $tasks = $this->scanner->scan();
        $count = $this->saveTasks($tasks);
        $deleted = $this->removeObsoleteTasks($tasks);

        return [
            'count' => $count,
            'deleted' => $deleted,
            'tasks' => $tasks,
        ];
    }

    private function saveTasks(array $tasks): int
    {
        $count = 0;

        foreach ($tasks as $taskData) {
            TodoTask::updateOrCreate(
                ['hash' => $taskData['hash']],
                $taskData
            );
            $count++;
        }

        return $count;
    }

    private function removeObsoleteTasks(array $tasks): int
    {
        $hashes = array_column($tasks, 'hash');

        return TodoTask::whereNotIn('hash', $hashes)->delete();
    }
}

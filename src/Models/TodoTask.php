<?php

namespace Prosvirin\LaravelTodoInspector\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TodoTask extends Model
{
    public function getTable(): string
    {
        return config('todo-inspector.table_name', 'todo_inspector_tasks');
    }

    protected $fillable = [
        'file_path',
        'line_number',
        'content',
        'type',
        'priority',
        'author',
        'assigned_to',
        'status',
        'hash',
    ];

    protected $casts = [
        'line_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const TYPE_TODO = 'TODO';

    public const TYPE_FIXME = 'FIXME';

    public const TYPE_HACK = 'HACK';

    public const TYPE_REVIEW = 'REVIEW';

    public const TYPE_NOTE = 'NOTE';

    public const TYPES = [
        self::TYPE_TODO,
        self::TYPE_FIXME,
        self::TYPE_HACK,
        self::TYPE_REVIEW,
        self::TYPE_NOTE,
    ];

    public const PRIORITY_LOW = 'LOW';

    public const PRIORITY_MEDIUM = 'MEDIUM';

    public const PRIORITY_HIGH = 'HIGH';

    public const PRIORITY_CRITICAL = 'CRITICAL';

    public const PRIORITIES = [
        self::PRIORITY_LOW => 'Low',
        self::PRIORITY_MEDIUM => 'Medium',
        self::PRIORITY_HIGH => 'High',
        self::PRIORITY_CRITICAL => 'Critical',
    ];

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_DONE = 'done';

    public const STATUS_WONT_FIX = 'wont_fix';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_DONE => 'Done',
        self::STATUS_WONT_FIX => "Won't Fix",
    ];

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_DONE, self::STATUS_WONT_FIX]);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_DONE, self::STATUS_WONT_FIX]);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeWithPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByAuthor(Builder $query, string $author): Builder
    {
        return $query->where('author', $author);
    }

    public function scopeAssignedTo(Builder $query, string $user): Builder
    {
        return $query->where('assigned_to', $user);
    }

    public function scopeInFile(Builder $query, string $filePath): Builder
    {
        return $query->where('file_path', 'like', "%{$filePath}%");
    }

    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_CRITICAL]);
    }

    public function getShortContentAttribute(): string
    {
        return mb_substr($this->content, 0, 100).(mb_strlen($this->content) > 100 ? '...' : '');
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_TODO => 'yellow',
            self::TYPE_FIXME => 'red',
            self::TYPE_HACK => 'purple',
            self::TYPE_REVIEW => 'blue',
            self::TYPE_NOTE => 'gray',
            default => 'gray',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_TODO => '📝',
            self::TYPE_FIXME => '🐛',
            self::TYPE_HACK => '🔧',
            self::TYPE_REVIEW => '👀',
            self::TYPE_NOTE => '📌',
            default => '📄',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_CRITICAL => 'red',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_MEDIUM => 'blue',
            self::PRIORITY_LOW => 'green',
            default => 'gray',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'gray',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_DONE => 'green',
            self::STATUS_WONT_FIX => 'red',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getFileNameAttribute(): string
    {
        return basename($this->file_path);
    }

    public function getDirectoryAttribute(): string
    {
        return dirname($this->file_path);
    }

    public function markAsDone(): bool
    {
        return $this->update(['status' => self::STATUS_DONE]);
    }

    public function markAsInProgress(): bool
    {
        return $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    public function markAsPending(): bool
    {
        return $this->update(['status' => self::STATUS_PENDING]);
    }

    public function assignTo(string $user): bool
    {
        return $this->update(['assigned_to' => $user]);
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isCritical(): bool
    {
        return $this->priority === self::PRIORITY_CRITICAL;
    }

    public function getFileLinkAttribute(): string
    {
        return 'phpstorm://open?file='.base_path($this->file_path)."&line={$this->line_number}";
    }

    public function getGithubLinkAttribute(): ?string
    {
        $githubRepo = config('todo-inspector.github_repo');

        if ($githubRepo) {
            return "https://github.com/{$githubRepo}/blob/main/{$this->file_path}#L{$this->line_number}";
        }

        return null;
    }
}

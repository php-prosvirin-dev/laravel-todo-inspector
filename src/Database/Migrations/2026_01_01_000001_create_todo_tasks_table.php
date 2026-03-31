<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the table name from config
     */
    private function getTableName(): string
    {
        return config('todo-inspector.table_name', 'todo_inspector_tasks');
    }

    public function up(): void
    {
        $tableName = $this->getTableName();

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->integer('line_number');
            $table->text('content');
            $table->string('type'); // TODO, FIXME, HACK, REVIEW, NOTE
            $table->string('priority')->default('MEDIUM');
            $table->string('author')->nullable();
            $table->string('assigned_to')->nullable();
            $table->string('status')->default('pending');
            $table->string('hash')->unique();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('priority');
            $table->index('file_path');
        });
    }

    public function down(): void
    {
        $tableName = $this->getTableName();

        Schema::dropIfExists($tableName);
    }
};

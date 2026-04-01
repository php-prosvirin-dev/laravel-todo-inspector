<?php

namespace Prosvirin\LaravelTodoInspector\Tests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Prosvirin\LaravelTodoInspector\TodoInspectorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set app key for encryption
        $this->app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

        // Set config for package
        $this->app['config']->set('todo-inspector.table_name', 'todo_inspector_tasks');

        // Create table manually
        Schema::create('todo_inspector_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->integer('line_number');
            $table->longText('content');
            $table->string('type');
            $table->string('priority')->default('LOW');
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

    protected function tearDown(): void
    {
        Schema::dropIfExists('todo_inspector_tasks');
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            TodoInspectorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testbench');
        config()->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
            'foreign_key_constraints' => true,
        ]);
    }
}
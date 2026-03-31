<?php

namespace Prosvirin\LaravelTodoInspector;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Prosvirin\LaravelTodoInspector\Classes\Scanner;
use Prosvirin\LaravelTodoInspector\Console\Commands\ScanTodosCommand;

class TodoInspectorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/todo-inspector.php',
            'todo-inspector'
        );

        $this->app->singleton(Scanner::class, function (Application $app) {
            return new Scanner;
        });

        $this->app->alias(Scanner::class, 'todo-inspector');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ScanTodosCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/todo-inspector.php' => config_path('todo-inspector.php'),
        ], 'todo-inspector-config');

        $this->publishes([
            __DIR__.'/Database/Migrations/' => database_path('migrations'),
        ], 'todo-inspector-migrations');

        //        $this->loadRoutesFrom(__DIR__.'/Http/Routes/web.php');
        //
        //        $this->loadViewsFrom(__DIR__.'/Resources/views', 'todo-inspector');
        //
        //        $this->publishes([
        //            __DIR__.'/Resources/views' => resource_path('views/vendor/todo-inspector'),
        //        ], 'todo-inspector-views');
    }
}

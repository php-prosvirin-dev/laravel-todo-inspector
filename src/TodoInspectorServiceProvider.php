<?php

namespace Prosvirin\LaravelTodoInspector;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Prosvirin\LaravelTodoInspector\Classes\Scanner;
use Prosvirin\LaravelTodoInspector\Console\Commands\ScanTodosCommand;
use Prosvirin\LaravelTodoInspector\Http\Middleware\BasicAuthMiddleware;
use Prosvirin\LaravelTodoInspector\Http\Middleware\LocaleMiddleware;

class TodoInspectorServiceProvider extends ServiceProvider
{
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

        $this->publishes([
            __DIR__.'/Resources/views' => resource_path('views/vendor/todo-inspector'),
        ], 'todo-inspector-views');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/todo-inspector'),
        ], 'todo-inspector-assets');

        $this->publishes([
            __DIR__.'/Resources/lang' => resource_path('lang/'),
        ], 'todo-inspector-lang');

        $this->app['config']->set('todo-inspector.available_locales', [
            'en', 'ru', 'uk', 'pl', 'de', 'fr', 'es', 'zh', 'ja',
        ]);

        $this->loadRoutesFrom(__DIR__.'/Http/Routes/web.php');

        $this->loadViewsFrom(__DIR__.'/Resources/views', 'todo-inspector');

        $this->loadViewsFrom(__DIR__.'/Resources/views/components', 'todo-inspector');

        $this->app['router']->aliasMiddleware('todo-inspector.auth', BasicAuthMiddleware::class);

        $this->app['router']->aliasMiddleware('todo-inspector.locale', LocaleMiddleware::class);
    }
}

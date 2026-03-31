<?php

namespace Prosvirin\LaravelTodoInspector\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->get('lang', session('locale', config('app.locale', 'en')));
        $availableLocales = array_keys(config('todo-inspector.locales', []));

        if (in_array($locale, $availableLocales)) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
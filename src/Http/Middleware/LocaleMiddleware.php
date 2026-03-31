<?php

namespace Prosvirin\LaravelTodoInspector\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->get('lang', session('locale', config('app.locale', 'en')));
        $supportedLocales = ['en', 'ru', 'uk', 'pl', 'de', 'fr', 'es', 'zh', 'ja'];

        if (in_array($locale, $supportedLocales)) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
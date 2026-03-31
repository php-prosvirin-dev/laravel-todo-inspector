<?php

namespace Prosvirin\LaravelTodoInspector\Services;

use Prosvirin\LaravelTodoInspector\Http\Requests\TodoIndexRequest;

class LocaleService
{
    public function setLocale(TodoIndexRequest $request): void
    {
        $locale = $request->get('lang', session('locale', config('app.locale', 'en')));
        $availableLocales = array_keys(config('todo-inspector.locales', []));

        if (in_array($locale, $availableLocales)) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }
    }

    public function getAvailableLocales(): array
    {
        return config('todo-inspector.locales', []);
    }

    public function getLocaleFlag(string $locale): string
    {
        return config("todo-inspector.locales.{$locale}.flag", '🏳️');
    }

    public function getLocaleName(string $locale): string
    {
        return config("todo-inspector.locales.{$locale}.name", $locale);
    }

    public function isLocaleSupported(string $locale): bool
    {
        return array_key_exists($locale, config('todo-inspector.locales', []));
    }
}

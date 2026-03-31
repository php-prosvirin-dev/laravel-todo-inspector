<?php

namespace Prosvirin\LaravelTodoInspector\Services;

use Prosvirin\LaravelTodoInspector\Http\Requests\TodoIndexRequest;

class LocaleService
{
    private array $supportedLocales = [
        'en', 'ru', 'uk', 'pl', 'de', 'fr', 'es', 'zh', 'ja',
    ];

    public function setLocale(TodoIndexRequest $request): void
    {
        $locale = $request->get('lang', session('locale', config('app.locale', 'en')));

        if (in_array($locale, $this->supportedLocales)) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }
    }

    public function getAvailableLocales(): array
    {
        return $this->supportedLocales;
    }
}

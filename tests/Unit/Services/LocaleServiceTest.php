<?php

use Prosvirin\LaravelTodoInspector\Services\LocaleService;
use Prosvirin\LaravelTodoInspector\Http\Requests\TodoIndexRequest;

it('sets locale from request', function () {
    $request = new TodoIndexRequest();
    $request->query->set('lang', 'ru');

    $service = new LocaleService();
    $service->setLocale($request);

    expect(app()->getLocale())->toBe('ru');
});

it('ignores unsupported locale', function () {
    $originalLocale = app()->getLocale();
    $request = new TodoIndexRequest();
    $request->query->set('lang', 'xx');

    $service = new LocaleService();
    $service->setLocale($request);

    expect(app()->getLocale())->toBe($originalLocale);
});

it('uses session locale when no lang in request', function () {
    session(['locale' => 'uk']);
    $request = new TodoIndexRequest();

    $service = new LocaleService();
    $service->setLocale($request);

    expect(app()->getLocale())->toBe('uk');
});

it('uses default locale when no session and no lang', function () {
    config(['app.locale' => 'en']);
    session()->forget('locale');
    $request = new TodoIndexRequest();

    $service = new LocaleService();
    $service->setLocale($request);

    expect(app()->getLocale())->toBe('en');
});

it('supports all configured locales', function () {
    $locales = ['en', 'ru', 'uk', 'pl', 'de', 'fr', 'es', 'zh', 'ja'];
    $service = new LocaleService();

    foreach ($locales as $locale) {
        $request = new TodoIndexRequest();
        $request->query->set('lang', $locale);
        $service->setLocale($request);
        expect(app()->getLocale())->toBe($locale);
    }
});

it('persists locale in session', function () {
    $request = new TodoIndexRequest();
    $request->query->set('lang', 'pl');

    $service = new LocaleService();
    $service->setLocale($request);

    expect(session('locale'))->toBe('pl');
});
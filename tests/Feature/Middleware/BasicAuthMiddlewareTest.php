<?php

use Prosvirin\LaravelTodoInspector\Http\Middleware\BasicAuthMiddleware;
use Illuminate\Http\Request;

beforeEach(function () {
    config(['todo-inspector.auth.login' => 'admin']);
    config(['todo-inspector.auth.password' => 'secret']);
});

it('allows access with correct credentials', function () {
    $request = Request::create('/todo-inspector', 'GET');
    $request->headers->set('PHP_AUTH_USER', 'admin');
    $request->headers->set('PHP_AUTH_PW', 'secret');
    $request->server->set('PHP_AUTH_USER', 'admin');
    $request->server->set('PHP_AUTH_PW', 'secret');

    $middleware = new BasicAuthMiddleware();
    $response = $middleware->handle($request, function () {
        return response('OK', 200);
    });

    expect($response->getStatusCode())->toBe(200);
});

it('denies access with incorrect credentials', function () {
    $request = Request::create('/todo-inspector', 'GET');
    $request->headers->set('PHP_AUTH_USER', 'wrong');
    $request->headers->set('PHP_AUTH_PW', 'wrong');
    $request->server->set('PHP_AUTH_USER', 'wrong');
    $request->server->set('PHP_AUTH_PW', 'wrong');

    $middleware = new BasicAuthMiddleware();
    $response = $middleware->handle($request, function () {
        return response('OK', 200);
    });

    expect($response->getStatusCode())->toBe(401);
});

it('denies access with no credentials', function () {
    $request = Request::create('/todo-inspector', 'GET');

    $middleware = new BasicAuthMiddleware();
    $response = $middleware->handle($request, function () {
        return response('OK', 200);
    });

    expect($response->getStatusCode())->toBe(401);
});

it('uses config values for authentication', function () {
    config(['todo-inspector.auth.login' => 'custom_user']);
    config(['todo-inspector.auth.password' => 'custom_pass']);

    $request = Request::create('/todo-inspector', 'GET');
    $request->headers->set('PHP_AUTH_USER', 'custom_user');
    $request->headers->set('PHP_AUTH_PW', 'custom_pass');
    $request->server->set('PHP_AUTH_USER', 'custom_user');
    $request->server->set('PHP_AUTH_PW', 'custom_pass');

    $middleware = new BasicAuthMiddleware();
    $response = $middleware->handle($request, function () {
        return response('OK', 200);
    });

    expect($response->getStatusCode())->toBe(200);
});

it('returns WWW-Authenticate header on failure', function () {
    $request = Request::create('/todo-inspector', 'GET');

    $middleware = new BasicAuthMiddleware();
    $response = $middleware->handle($request, function () {
        return response('OK', 200);
    });

    expect($response->headers->get('WWW-Authenticate'))->toBe('Basic realm="Todo Inspector"');
});
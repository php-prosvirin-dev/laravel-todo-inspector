<?php

namespace Prosvirin\LaravelTodoInspector\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BasicAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $login = config('todo-inspector.auth.login', env('TODO_INSPECTOR_LOGIN', 'admin'));
        $password = config('todo-inspector.auth.password', env('TODO_INSPECTOR_PASSWORD', 'password'));

        if ($request->getUser() !== $login || $request->getPassword() !== $password) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="Todo Inspector"',
            ]);
        }

        return $next($request);
    }
}

<?php

declare(strict_types=1);

namespace Larexsetch\LaravelTransactional\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Larexsetch\LaravelTransactional\Attributes\Transactional;

final class TransactionalMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        if ($route === null) {
            return $next($request);
        }

        $controllerAction = $route->getAction('controller');
        if (!$controllerAction || !is_string($controllerAction) || !str_contains($controllerAction, '@')) {
            return $next($request);
        }

        [$controllerClass, $methodName] = explode('@', $controllerAction);
        $reflection = new \ReflectionMethod($controllerClass, $methodName);
        $attribute = $reflection->getAttributes(Transactional::class)[0] ?? null;

        if ($attribute) {
            $transactional = $attribute->newInstance();

            return DB::connection($transactional->connection)->transaction(fn() => $next($request));
        }

        return $next($request);
    }

}

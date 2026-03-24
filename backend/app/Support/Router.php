<?php

declare(strict_types=1);

namespace App\Support;

final class Router
{
    /** @var array<string, array<int, array{pattern:string, handler:callable}>> */
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[strtoupper($method)][] = ['pattern' => $pattern, 'handler' => $handler];
    }

    public function dispatch(Request $request): array
    {
        $methodRoutes = $this->routes[$request->method] ?? [];

        foreach ($methodRoutes as $route) {
            $regex = '#^' . preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $route['pattern']) . '$#';
            if (preg_match($regex, $request->path, $matches) === 1) {
                $params = array_filter($matches, static fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
                return ($route['handler'])($request, $params);
            }
        }

        return ['status' => 404, 'body' => ['error' => 'Route not found', 'path' => $request->path]];
    }
}

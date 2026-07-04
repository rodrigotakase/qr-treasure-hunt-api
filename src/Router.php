<?php

namespace App;

class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = ['method' => 'GET', 'pattern' => $pattern, 'handler' => $handler];
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->routes[] = ['method' => 'POST', 'pattern' => $pattern, 'handler' => $handler];
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $params = $this->match($route['pattern'], $path);
            if ($params !== null) {
                $route['handler'](...$params);
                return;
            }
        }
        $this->notFound($path);
    }

    private function match(string $pattern, string $path): ?array
    {
        $patternParts = explode('/', trim($pattern, '/'));
        $pathParts = explode('/', trim($path, '/'));
        if (count($patternParts) !== count($pathParts)) {
            return null;
        }
        $params = [];
        foreach ($patternParts as $index => $part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                if ($pathParts[$index] === '') {
                    return null;
                }
                $params[] = rawurldecode($pathParts[$index]);
            } elseif ($part !== $pathParts[$index]) {
                return null;
            }
        }
        return $params;
    }

    private function notFound(string $path): void
    {
        if (str_starts_with($path, '/api/')) {
            Response::error('Not found', 404);
        }
        Response::html('<h1>404 Not Found</h1>', 404);
    }
}

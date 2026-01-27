<?php

namespace App\Core;

/**
 * Router - Handles HTTP routing
 */
class Router
{
    private array $routes = [];
    private array $middleware = [];

    /**
     * Add GET route
     */
    public function get(string $path, $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    /**
     * Add POST route
     */
    public function post(string $path, $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * Add PUT route
     */
    public function put(string $path, $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Add DELETE route
     */
    public function delete(string $path, $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Add route
     */
    private function addRoute(string $method, string $path, $handler): self
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => []
        ];

        return $this;
    }

    /**
     * Add middleware to last added route
     */
    public function middleware($middleware): self
    {
        if (!empty($this->routes)) {
            $lastIndex = count($this->routes) - 1;
            $this->routes[$lastIndex]['middleware'][] = $middleware;
        }

        return $this;
    }

    /**
     * Resolve and execute route
     */
    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove base path if exists
        $basePath = $this->getBasePath();
        if ($basePath && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $pattern = $this->convertToRegex($route['path']);

                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches); // Remove full match

                    // Remove named keys, keep only numeric indices
                    $matches = array_filter($matches, function ($key) {
                        return is_int($key);
                    }, ARRAY_FILTER_USE_KEY);

                    // Re-index the array to ensure consecutive numeric keys
                    $matches = array_values($matches);

                    // Execute middleware
                    foreach ($route['middleware'] as $middleware) {
                        if (is_callable($middleware)) {
                            $middleware();
                        }
                    }

                    // Execute handler
                    $this->executeHandler($route['handler'], $matches);
                    return;
                }
            }
        }

        // No route found
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Route not found']);
    }

    /**
     * Execute route handler
     */
    private function executeHandler($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
        } elseif (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $method] = $handler;
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $params);
        }
    }

    /**
     * Convert route path to regex pattern
     */
    private function convertToRegex(string $path): string
    {
        // Replace {param} with named capture groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);

        // Replace :param with numbered capture groups
        $pattern = preg_replace('/:([a-zA-Z0-9_]+)/', '([^/]+)', $pattern);

        return '#^' . $pattern . '$#';
    }

    /**
     * Get base path from script location
     */
    private function getBasePath(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($scriptName);

        return $scriptDir === '/' ? '' : $scriptDir;
    }
}

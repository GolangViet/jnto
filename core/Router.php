<?php

declare(strict_types=1);

namespace Core;

use Closure;
use RuntimeException;

final class Router
{
    private array $routes = [];

    /**
     * Router constructor.
     *
     * @param Request $request The current HTTP request.
     * @param Response $response The response helper used for outputs and redirects.
     */
    public function __construct(
        private readonly Request $request,
        private readonly Response $response
    ) {}

    /**
     * Register a GET route.
     *
     * @param string $path Route path pattern.
     * @param callable|array $handler Handler callable or [ControllerClass, 'method'].
     * @param array $middleware List of middleware class names to run before handler.
     * @return void
     */
    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    /**
     * Register a POST route.
     *
     * @param string $path Route path pattern.
     * @param callable|array $handler Handler callable or [ControllerClass, 'method'].
     * @param array $middleware List of middleware class names to run before handler.
     * @return void
     */
    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    /**
     * Register a PUT route.
     *
     * @param string $path Route path pattern.
     * @param callable|array $handler Handler callable or [ControllerClass, 'method'].
     * @param array $middleware List of middleware class names to run before handler.
     * @return void
     */
    public function put(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('PUT', $path, $handler, $middleware);
    }

    /**
     * Register a DELETE route.
     *
     * @param string $path Route path pattern.
     * @param callable|array $handler Handler callable or [ControllerClass, 'method'].
     * @param array $middleware List of middleware class names to run before handler.
     * @return void
     */
    public function delete(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('DELETE', $path, $handler, $middleware);
    }

    /**
     * Add a route to the router table.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE, ...).
     * @param string $path Route path.
     * @param callable|array $handler Handler callable or [ControllerClass, 'method'].
     * @param array $middleware Middleware list.
     * @return void
     */
    private function add(string $method, string $path, callable|array $handler, array $middleware): void
    {
        $path = '/' . trim($path, '/');
        $path = $path === '//' ? '/' : $path;
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path);

        $this->routes[$method][] = [
            'path' => $path,
            'pattern' => '#^' . $pattern . '$#',
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * Dispatch the current request to the matching route handler.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $method = $this->request->method();
        $path = $this->request->path();

        foreach ($this->routes[$method] ?? [] as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            foreach ($route['middleware'] as $middlewareClass) {
                $middleware = new $middlewareClass();
                if (!$middleware->handle($this->request, $this->response)) {
                    return;
                }
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $this->invoke($route['handler'], $params);
            return;
        }

        $this->response->status(404);
        echo View::render('errors/404', [], false);
    }

    /**
     * Invoke the route handler with resolved parameters.
     *
     * @param callable|array $handler Handler callable or [ControllerClass, 'method'].
     * @param array $params Route parameters to pass to the handler.
     * @return void
     */
    private function invoke(callable|array $handler, array $params): void
    {
        if ($handler instanceof Closure) {
            echo $handler(...array_values($params));
            return;
        }

        [$class, $method] = $handler;
        if (!class_exists($class)) {
            throw new RuntimeException("Controller {$class} not found.");
        }

        $controller = new $class();
        $result = $controller->{$method}(...array_values($params));

        if (is_string($result)) {
            echo $result;
        }
    }
}

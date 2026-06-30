<?php

namespace App\Core;

use Exception;

class Router {
    protected array $routes = [];
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, array|callable $callback, array $middlewares = []): void {
        $this->routes['GET'][$this->convertToRegex($path)] = [
            'callback' => $callback,
            'middlewares' => $middlewares,
            'original_path' => $path
        ];
    }

    public function post(string $path, array|callable $callback, array $middlewares = []): void {
        $this->routes['POST'][$this->convertToRegex($path)] = [
            'callback' => $callback,
            'middlewares' => $middlewares,
            'original_path' => $path
        ];
    }

    private function convertToRegex(string $path): string {
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_\-]+)', $path);
        return '#^' . $regex . '$#';
    }

    public function resolve(): void {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();

        // Strip trailing slash except if path is just /
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        $routesForMethod = $this->routes[$method] ?? [];
        $matchedRoute = null;
        $params = [];

        foreach ($routesForMethod as $regex => $routeInfo) {
            if (preg_match($regex, $path, $matches)) {
                $matchedRoute = $routeInfo;
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                break;
            }
        }

        if (!$matchedRoute) {
            $this->response->setStatusCode(404);
            if ($this->request->isAjax()) {
                $this->response->json(['error' => 'Not Found', 'message' => "Route '{$path}' for method '{$method}' not found"], 404);
            } else {
                $this->response->renderView('errors/404', ['message' => "Page not found"], 'error');
            }
            return;
        }

        $this->request->setRouteParams($params);
        $callback = $matchedRoute['callback'];
        $middlewares = $matchedRoute['middlewares'];

        // Execute Middlewares
        foreach ($middlewares as $middlewareClass) {
            if (class_exists($middlewareClass)) {
                $middleware = new $middlewareClass();
                $middleware->handle($this->request, $this->response);
            }
        }

        if (is_callable($callback)) {
            call_user_func($callback, $this->request, $this->response);
            return;
        }

        if (is_array($callback)) {
            $controllerClass = $callback[0];
            $action = $callback[1];

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $action)) {
                    $controller->$action($this->request, $this->response);
                    return;
                }
            }
        }

        $this->response->setStatusCode(500);
        throw new Exception("Invalid route callback or action not found");
    }
}

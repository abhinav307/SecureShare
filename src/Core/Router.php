<?php

namespace App\Core;

class Router
{
    protected $routes = [];

    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    protected function addRoute($method, $uri, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

    public function dispatch($uri, $method)
    {
        // Remove query string from URI
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                // Convert route to regex: e.g. /chat/{id} -> /chat/([a-zA-Z0-9_-]+)
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $route['uri']);
                $pattern = "@^" . $pattern . "$@D";

                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // Remove the full match
                    
                    $action = $route['action'];
                    if (is_callable($action)) {
                        return call_user_func_array($action, $matches);
                    } elseif (is_array($action)) {
                        $controller = new $action[0]();
                        $methodName = $action[1];
                        return call_user_func_array([$controller, $methodName], $matches);
                    }
                }
            }
        }

        // 404 Route Not Found
        http_response_code(404);
        echo "404 Not Found";
    }
}

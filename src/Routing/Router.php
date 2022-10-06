<?php

declare(strict_types=1);

namespace Src\Routing;

use Src\Routing\Exceptions\RouteNotFoundException;

class Router
{
    private array $routes;

    public function register(string $requestMethod, string $route, callable|array $action)
    { 
        $this->routes[$requestMethod][$route] = $action;

        // if ($_SERVER['REQUEST_URI'] === $route) {
        //     call_user_func([$this, 'resolve'], $_SERVER['REQUEST_URI'], strtolower($_SERVER['REQUEST_METHOD']));
        // }

        return $this;
    }

    public function get(string $route, callable|array $action): self
    {
        return $this->register('get', $route, $action);
    }

    public function post(string $route, callable|array $action): self
    {
        return $this->register('post', $route, $action);
    }

    public function routes()
    {
        return $this->routes;
    }

    public function resolve(string $requestUri, string $requestMethod)
    {
        $route = explode('?', $requestUri)[0];
        $action = $this->routes[$requestMethod][$route] ?? null;

        if (!$action) {
            throw new RouteNotFoundException();
        }

        if (is_callable($action)) {
            return call_user_func($action);
        }

        if (is_array($action)) {

            [$class, $method] = $action;

            if (class_exists($class)) {
                $calss = new $class();

                if (method_exists($calss, $method)) {
                    return call_user_func_array([$calss, $method], []);
                }
            }
        }
        
        throw new RouteNotFoundException();
    }
}

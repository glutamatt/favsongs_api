<?php

namespace Lib;

class Routing
{
    protected $routes = [];

    public function add($pattern, $controller, $methods = ['GET'])
    {
        $this->routes[] = ['#^' . $pattern . "/?$#", $methods, $controller];
    }

    public function match($uri, $method = 'GET')
    {
        foreach ($this->routes as $route) {
            if (in_array($method, $route[1]) && preg_match($route[0], $uri, $parameters)) {
                return function () use ($route, $parameters) {
                    return call_user_func_array($route[2], array_slice($parameters, 1));
                };
            }
        }

        return false;
    }
}

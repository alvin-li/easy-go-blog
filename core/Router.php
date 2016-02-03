<?php

namespace Core;

use \Exception;

class Router
{
    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_replace('//', '', $uri);
        $uri = trim($uri, '/');

        list($controller, $method) = explode('/', $uri);
        $fullClass = 'Controllers\\'.$controller;
        if (class_exists($fullClass)) {
            $controllerObj = new $fullClass;
        } else {
            throw new \Exception('class '.$fullClass.' not found', CLASS_NOT_FOUND_CODE);
        }

        if (method_exists($controllerObj, $method)) {
            $controllerObj->$method();
        } else {
            throw new \Exception('class '.$fullClass.' method '.$method.' not found', CLASS_METHOD_NOT_FOUND_CODE);
        }
    }
}
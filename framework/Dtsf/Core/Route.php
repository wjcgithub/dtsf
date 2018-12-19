<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:25
 */

namespace Dtsf\Core;


class Route
{
    public static function dispatch($path)
    {
        if (empty($path) || '/' == $path){
            $controller = 'Index';
            $method = 'index';
        } else {
            $maps = explode('/', $path);
            $controller = ucfirst($maps[1]);
            $method = $maps[2];
        }

        $controllerClass = 'App\\Controller\\'.$controller;
        $class = new $controllerClass;

        return $class->$method();
    }
}
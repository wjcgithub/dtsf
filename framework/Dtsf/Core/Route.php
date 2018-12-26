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
            $controller = 'IndexController';
            $method = 'index';
        } else {
            $maps = explode('/', $path);
            $controller = ucfirst($maps[1]);
            $method = $maps[2];
        }

        $controllerClass = 'App\\Controller\\'.$controller.'Controller';
        $class = new $controllerClass;

        return $class->$method();
    }
}
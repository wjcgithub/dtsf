<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:25
 */

namespace Dtsf\Core;


use Dtsf\Mvc\Controller;
use Dtsf\Pool\ContextPool;
use FastRoute\Dispatcher;

class Route
{
    public static function dispatch()
    {
        //从上下文里面获取请求信息
        $context = ContextPool::getContext();
        $request = $context->getRequest();
        $path = $request->getUri()->getPath();
        //获取自己配置的路由规则
        $r = Config::get('router');
        //没有路由配置或者配置不可执行, 则走默认路由
        if (empty($r) || !is_callable($r)) {
            return self::normal($path, $request);
        }

        //引入fastrouter, 进行路由检测
        $dispatcher = \FastRoute\simpleDispatcher($r);
        $routeInfo = $dispatcher->dispatch($request->getMethod(), $path);
        //匹配到了
        if (Dispatcher::FOUND === $routeInfo[0]) {
            //匹配的是数组, 格式: ['controllerName', 'MethodName']
            if (is_array($routeInfo[1])) {
                if (!empty($routeInfo[2]) && is_array($routeInfo[2])) {
                    //有默认参数
                    $params = $request->getQueryParams() + $routeInfo[2];
                    $request->withQueryParams($params);
                }

                $request->withAttribute(Controller::_CONTROLLER_KEY_, $routeInfo[1][0]);
                $request->withAttribute(Controller::_METHOD_KEY_, $routeInfo[1][1]);
                $controller = new $routeInfo[1][0]();
                $methodName = $routeInfo[1][1];
                $result = $controller->$methodName();
            } elseif (is_string($routeInfo[1])) {
                //字符串, 格式: controllerName@MethodName
                list($controllerName, $methodName) = explode('@', $routeInfo[1]);
                if (!empty($routeInfo[2]) && is_array($routeInfo[2])) {
                    //有默认参数
                    $params = $request->getQueryParams() + $routeInfo[2];
                    $request->withQueryParams($params);
                }

                $request->withAttribute(Controller::_CONTROLLER_KEY_, $controllerName);
                $request->withAttribute(Controller::_METHOD_KEY_, $methodName);
                $controller = new $controllerName();
                $result = $controller->$methodName();
            } elseif (is_callable($routeInfo[1])) {
                //回调函数, 直接执行
                $result = $routeInfo[1](...$routeInfo[2]);
            } else {
                throw new \InvalidArgumentException('router error');
            }

            return $result;
        }

        //如果没有找到路由, 走默认的路由 http://dtsf.com/{controllerName}/{MethodName}
        if (Dispatcher::NOT_FOUND == $routeInfo[0]) {
            return self::normal($path, $request);
        }

        //匹配到了, 但不允许的http method
        if (Dispatcher::METHOD_NOT_ALLOWED === $routeInfo[0]) {
            throw new \RuntimeException('method not allowed');
        }

    }

    /**
     * @desc 没有配置路由的处理
     *
     * @param $path
     * @param $request
     * @return mixed
     */
    public static function normal($path, $request)
    {
        //默认访问Controller/IndexController.php的index方法
        $controllerName = '';
        $methodName = '';
        if (empty($path) || '/' == $path) {
            $controllerName = 'index';
            $methodName = 'index';
        } else {
            $maps = explode('/', $path);

            if (count($maps) < 2) {
                $controllerName = 'index';
                $methodName = 'index';
            } else {
                $controllerName = ucfirst($maps[1]);
                if (empty($maps[2])) {
                    $methodName = 'index';
                } else {
                    $methodName = $maps[2];
                }
            }
        }
        $controllerName = 'App\\Controller\\' . $controllerName . 'Controller';
        $request->withAttribute(Controller::_CONTROLLER_KEY_, $controllerName);
        $request->withAttribute(Controller::_METHOD_KEY_, $methodName);
        $controller = new $controllerName();
        return $controller->$methodName();
    }
}
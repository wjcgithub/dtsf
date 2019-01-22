<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-17
 * Time: 下午5:35
 */

namespace Dtsf\Core;


use DI\Container;
use DI\ContainerBuilder;
use Dtsf\Dtsf;

class WorkerApp
{
    use Singleton;

    private $app = null;
    private $container = null;

    public function init()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAnnotations(true);
        $this->container = $containerBuilder->build();
        require_once Dtsf::$frameworkPath . DS . 'Dtsf' . DS . 'Helper' . DS . 'dtFunc.php';
    }

    public function getApp()
    {
        return $this->app;
    }

    public function setApp($app)
    {
        $this->app = $app;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
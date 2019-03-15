<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-17
 * Time: 下午5:35
 */

namespace Dtsf\Core;


//use DI\Container;
//use DI\ContainerBuilder;

class WorkerApp
{
    use Singleton;

    const WORKERSTARTED = 1;
    const WORKERSTOPED = 2;
    const WORKEREXIT = 3;
    const WORKERLASTACK = 4;

    private $app = null;
    private $container = null;
    //serverStatus 1:running, 2:stop
    private $attributes = [];

    private function __construct()
    {
        $this->debugDirName = 'debuginfo';
        $this->ackErrorDirName = 'mq_ack_error';
    }

    public function init()
    {
//        $containerBuilder = new ContainerBuilder();
//        $containerBuilder->useAnnotations(true);
//        $this->container = $containerBuilder->build();
//        require_once Dtsf::$frameworkPath . DS . 'Dtsf' . DS . 'Helper' . DS . 'dtFunc.php';
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

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return null;
    }

    public function workerStarted()
    {
        $this->serverStatus = self::WORKERSTARTED;
    }

    public function workerStoped()
    {
        $this->serverStatus = self::WORKERSTOPED;
    }

    public function workerExit()
    {
        $this->serverStatus = self::WORKEREXIT;
    }

    public function workerSetStatus(int $status)
    {
        $this->serverStatus = $status;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:04
 */

namespace Dtsf\Mvc;


use Dtsf\Coroutine\Coroutine;
use Dtsf\Pool\RedisPool;

class RedisDao
{
    private static $instance;
    /**
     * @var redis连接数组
     * @desc 不同协程不能复用redis连接，所以通过协程id进行资源隔离
     */
    private $dbs;

    /**
     * @var Redis
     */
    private $redis;

    public static function getInstance($name = 'default')
    {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = new static($name);
        }

        return self::$instance[$name];
    }


    public function __construct($name)
    {
        $coId = Coroutine::getId();
        if (empty($this->dbs[$coId])) {
            //不同协程不能复用mysql连接，所以通过协程id进行资源隔离
            //达到同一协程只用一个mysql连接，不同协程用不同的mysql连接
            $this->dbs[$coId] = RedisPool::getInstance($name)->get();
            defer(function () use ($name){
                //利用协程的defer特性，自动回收资源
                $this->recycle($name);
            });
        }
        $this->redis = $this->dbs[$coId];
    }

    /**
     * @throws \Exception
     * @desc mysql资源回收到连接池
     */
    public function recycle($name)
    {
        $coId = Coroutine::getId();
        if (!empty($this->dbs[$coId])) {
            $redis = $this->dbs[$coId];
            unset($this->dbs[$coId]);
            try{
                $redis->ping();
                RedisPool::getInstance($name)->put($redis);
            }catch (\Exception $e) {
            }

        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @desc 利用__call,实现操作mysql,并能做断线重连等相关检测
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->redis, $name), $arguments);
    }
}
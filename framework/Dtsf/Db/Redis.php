<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午4:22
 */

namespace Dtsf\Db;

use Dtsf\Core\Log;
use Swoole\Coroutine\Redis as SwRedis;

class Redis
{
    /**
     * @var MySQL
     */
    private $redis;
    private $config;    //数据库配置

    /**
     * @param $config
     * @return mixed
     * @throws \Exception
     * @desc 连接mysql
     */
    public function connect($config)
    {
        $this->config = $config;
        //创建主数据连接
        $redis = new SwRedis($this->config['options']);
        $redis->connect($this->config['host'], $this->config['port']);
        $res = $redis->connected;
        if ($res === false) {
            //连接失败，抛弃常
            throw new \RuntimeException('Failed to connect redis server:' . $redis->errMsg, $redis->errCode);
        }
        $this->redis = $redis;
        return $res;
    }

    /**
     * 获取client
     * @return redis
     */
    protected function getRedis()
    {
        return $this->redis;
    }

    protected function closeRedis()
    {
        $this->redis->close();
    }

    /**
     * @param $type
     * @param $index
     * @return MySQL
     * @desc 单个数据库重连
     * @throws \Exception
     */
    public function reconnect()
    {
        //创建主数据连接
        $redis = new SwRedis($this->config['options']);
        $redis->connect($this->config['host'], $this->config['port']);
        $res = $redis->connected;
        if ($res === false) {
            //连接失败，抛弃常
            throw new \RuntimeException($redis->errMsg, $redis->errCode);
        } else {
            $this->redis = $redis;
        }
        return $redis;
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
        $result = call_user_func_array(array($this->redis, $name), $arguments);
        if (false === $result) {
            Log::warning('redis query false'. $name . '=====' . json_encode($arguments));
            if (!$this->redis->connected) { //断线重连
                $this->reconnect();
                Log::info('redis reconnect' . $name . '=====' . json_encode($arguments));
                $result = call_user_func_array(array($this->redis, $name), $arguments);
            }

            if (!empty($this->redis->errCode)) {  //有错误码，则抛出弃常
                Log::error('redis reconnect error');
                throw new \RuntimeException($this->redis->errMsg, $this->redis->errCode);
            }
        }
        return $result;
    }
}
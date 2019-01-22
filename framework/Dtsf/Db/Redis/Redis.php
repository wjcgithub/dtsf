<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午4:22
 */

namespace Dtsf\Db\Redis;

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
        if(!empty($this->config['auth'])){
            $redis->auth($this->config['auth']);
        }
        $redis->select($this->config['db']);
        $res = $redis->connected;
        if ($res === false) {
            //连接失败，抛弃常
            throw new DtRedisReException('Failed to connect redis server:' . $redis->errMsg, $redis->errCode);
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
        $this->connect($this->config['options']);
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
        $result = null;
        try{
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
                    throw new DtRedisReException($this->redis->errMsg, $this->redis->errCode);
                }
            }
        }catch (\Exception $e) {
            throw new DtRedisReException($e->getMessage(), $this->redis->errCode, $e);
        }

        return $result;
    }
}
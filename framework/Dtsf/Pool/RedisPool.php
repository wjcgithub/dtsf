<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午4:32
 */

namespace Dtsf\Pool;


use Dtsf\Core\Config;
use Dtsf\Db\Redis;

class RedisPool
{
    private static $instance;
    private $pool;
    private $config;


    /**
     * 获取一个链接池实例
     *
     * @param null $config
     * @return static
     */
    public static function getInstance($name = 'default')
    {
//        print_r($name);
        if (empty($config = Config::get('redis'))) {
            throw new \RuntimeException("Redis config empty");
        }

        if (!empty($config[$name]) && empty(self::$instance[$name])) {
            self::$instance[$name] = new static($config[$name]);
        }

        return self::$instance[$name];
    }

    /**
     * 初始化链接池
     *
     * MysqlPool constructor.
     * @param $config
     */
    private function __construct($config)
    {
        if (empty($this->pool)) {
            $this->config = $config;
            $this->pool = new \chan($config['pool_size']);
            for ($i = 0; $i < $config['pool_size']; $i++) {
                $redis = new Redis();
                $res = $redis->connect($config);
                if ($res === false) {
                    throw new \RuntimeException('Failed to connect redis server.');
                } else {
                    $this->put($redis);
                }
            }
        }
    }

    /**
     * 将链接池加入队列
     *
     * @param $mysql
     */
    public function put($redis)
    {
        $this->pool->push($redis);
    }

    /**
     * 获取一个可用mysql链接
     *
     * @return mixed
     */
    public function get()
    {
        //@todo 如果为空要从新初始化连接池
        $redis = $this->pool->pop($this->config['pool_get_timeout']);
        if (false === $redis) {
            throw new \RuntimeException("Get redis timeout on coroutine, all mysql connection is used");
        }

        return $redis;
    }

    /**
     * 获取连接池中数量
     *
     * @return mixed
     */
    public function getLength()
    {
        return $this->pool->length();
    }
}
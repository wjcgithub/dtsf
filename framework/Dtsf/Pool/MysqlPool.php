<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午4:32
 */

namespace Dtsf\Pool;


use Dtsf\Db\Mysql;
use RuntimeException;

class MysqlPool
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
    public static function getInstance($config = null)
    {
        if (empty(self::$instance)) {
            if (empty($config)) {
                throw new RuntimeException("Mysql config empty");
            }
            self::$instance = new static($config);
        }

        return self::$instance;
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
                $mysql = new Mysql();
                $res = $mysql->connect($config);
                if ($res == false) {
                    throw new RuntimeException('Failed to connect mysql server.');
                } else {
                    $this->put($mysql);
                }
            }
        }
    }

    /**
     * 将链接池加入队列
     *
     * @param $mysql
     */
    public function put($mysql)
    {
        $this->pool->push($mysql);
    }

    /**
     * 获取一个可用mysql链接
     *
     * @return mixed
     */
    public function get()
    {
        $mysql = $this->pool->pop($this->config['pool_get_timeout']);
        if (false === $mysql) {
            throw new RuntimeException("Get mysql timeout, all mysql connection is used");
        }

        return $mysql;
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
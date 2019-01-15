<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:07
 */

namespace App\Dao;


use Dtsf\Core\Singleton;
use Dtsf\Mvc\Dao;
use Dtsf\Mvc\DbDao;
use Dtsf\Mvc\RedisDao;

class RabbitMqDao extends Dao
{
    use Singleton;

    protected $daoType = 'rabbitmq';
    /**
     * @var string mysql 链接
     */
    protected $connection = '';

    public function __construct($config='default')
    {
        $this->connection = $config;
    }

    public function insert($task, $payload, $route_key)
    {
        return $this->getDb()->PostTask($task, $payload, true, $route_key);
    }
}
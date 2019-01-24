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
use Swoole\Coroutine;

class CeleryMqDao extends Dao
{
    use Singleton;

    protected $daoType = 'celery';
    /**
     * @var string mysql 链接
     */
    protected $connection = '';

    public function __construct($config = 'default')
    {
        $this->connection = $config;
    }

    public function insert($msgid, $task, $payload, $route_key)
    {
        return $this->getDb()->PostTask($msgid, $task, $payload, true, $route_key);
    }
}
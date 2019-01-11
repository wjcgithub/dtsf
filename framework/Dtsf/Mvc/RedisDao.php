<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:04
 */

namespace Dtsf\Mvc;


class RedisDao extends Dao
{
    protected $daoType = 'redis';
    /**
     * @var string mysql 链接
     */
    protected $connection = '';

    public function __construct($config)
    {
        $this->connection = $config;
    }
}
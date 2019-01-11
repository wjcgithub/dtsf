<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:07
 */

namespace App\Dao;


use Dtsf\Core\Singleton;
use Dtsf\Mvc\DbDao;
use Dtsf\Mvc\RedisDao;

class RedisDefaultDao extends RedisDao
{
    use Singleton;

    public function __construct()
    {
        parent::__construct('default');
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->getDb(), $name), $arguments);
    }
}
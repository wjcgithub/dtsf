<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-28
 * Time: 下午4:56
 */

namespace App\Service;


use Dtsf\Core\Singleton;
use Dtsf\Mvc\RedisDao;

class RedisService
{
    use Singleton;

    public function get($key)
    {
        $r1 = RedisDao::getInstance('db')->get($key);
        $r2 = RedisDao::getInstance()->get($key);
        return json_encode(compact('r1', 'r2'));
    }
}
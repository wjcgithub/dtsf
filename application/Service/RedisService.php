<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-28
 * Time: 下午4:56
 */

namespace App\Service;


use App\Dao\RedisDefaultDao;

class RedisService extends AbstractService
{
    public function get($key=null)
    {
        RedisDefaultDao::getInstance()->set('a',1111);
        $r1 = RedisDefaultDao::getInstance()->get('a');
        $r2 = 'test';
//        $r1 = RedisDao::getInstance('db')->get($key);
//        $r2 = RedisDao::getInstance('default')->get($key);
        return json_encode(compact('r1', 'r2'));
    }
}
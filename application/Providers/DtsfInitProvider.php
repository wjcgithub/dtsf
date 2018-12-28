<?php
namespace App\Providers;
use Dtsf\Core\Config;
use Dtsf\Mvc\RedisDao;
use Dtsf\Pool\MysqlPool;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-28
 * Time: 下午5:36
 */
class DtsfInitProvider
{
    public static function poolInit()
    {
        $mysqlConfig = Config::get('mysql');
        if (!empty($mysqlConfig)){
            //初始化mysql链接池
            MysqlPool::getInstance($mysqlConfig);
        }

        $redisConfig = Config::get('redis');
        if (!empty($redisConfig)){
            foreach ($redisConfig as $name=>$c) {
                RedisDao::getInstance($name);
            }
        }
    }
}
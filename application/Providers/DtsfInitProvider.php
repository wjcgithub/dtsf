<?php
namespace App\Providers;
use Dtsf\Core\Config;
use Dtsf\Mvc\RedisDao;
use App\Utils\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;

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
        $mysqlConfig = Config::get('mysql.default');
        if (!empty($mysqlConfig)){
            //初始化mysql链接池
            PoolManager::getInstance()->register($mysqlConfig['class'], $mysqlConfig['pool_size']);
        }

//        $redisConfig = Config::get('redis');
//        if (!empty($redisConfig)){
//            foreach ($redisConfig as $name=>$c) {
//                RedisDao::getInstance($name);
//            }
//        }
    }
}
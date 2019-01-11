<?php
namespace App\Providers;

use Dtsf\Core\Config;
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
        if (!empty($mysqlConfig)) {
            PoolManager::getInstance()->register($mysqlConfig['class'], $mysqlConfig['pool_size']);
        }

        $redisConfig = Config::get('redis.default');
        if (!empty($redisConfig)) {
            PoolManager::getInstance()->register($redisConfig['class'], $redisConfig['pool_size']);
        }
    }
}
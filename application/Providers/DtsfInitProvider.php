<?php
namespace App\Providers;

use Dtsf\Core\Config;
use Dtsf\Core\Log;
use EasySwoole\Component\Pool\PoolManager;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-28
 * Time: 下午5:36
 */
class DtsfInitProvider
{
    public static function workerStart($worker_id)
    {
        if( ($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0){
            $mysqlConfig = Config::get('mysql.default');
            if (!empty($mysqlConfig)) {
                PoolManager::getInstance()->register($mysqlConfig['class'], $mysqlConfig['pool_size']);
            }

            $redisConfig = Config::get('redis.default');
            if (!empty($redisConfig)) {
                PoolManager::getInstance()->register($redisConfig['class'], $redisConfig['pool_size']);
            }
        }else{
            $rabbitmqConfig = Config::get('rabbitmq.default');
            if (!empty($rabbitmqConfig)) {
                PoolManager::getInstance()->register($rabbitmqConfig['class'], $rabbitmqConfig['pool_size']);
            }
        }
    }

    public static function workerStop($worker_id)
    {
        if( ($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0){
            Log::info('stop', [], 'worker');
            $mysqlConfig = Config::get('mysql.default');
            if (!empty($mysqlConfig)) {
                go(function () use ($mysqlConfig){
                    PoolManager::getInstance()->getPool($mysqlConfig['class'], $mysqlConfig['pool_size'])->gcObject(0);
                });
            }

            $redisConfig = Config::get('redis.default');
            if (!empty($redisConfig)) {
                go(function () use ($redisConfig){
                    PoolManager::getInstance()->getPool($redisConfig['class'], $redisConfig['pool_size'])->gcObject(0);
                });
            }
        }else{
            $rabbitmqConfig = Config::get('rabbitmq.default');
            if (!empty($rabbitmqConfig)) {
                go(function () use ($rabbitmqConfig){
                    PoolManager::getInstance()->getPool($rabbitmqConfig['class'], $rabbitmqConfig['pool_size'])->gcObject(0);
                });
            }
        }
    }
}
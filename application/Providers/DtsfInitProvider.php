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
        if (($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0) {
            $mysqlConfig = Config::get('mysql.default');
            if (!empty($mysqlConfig)) {
                PoolManager::getInstance()->register($mysqlConfig['class'], $mysqlConfig['pool_size'])
                    ->setIntervalCheckTime($mysqlConfig['interval_check_time'])
                    ->setMaxIdleTime($mysqlConfig['max_idle_time'])
                    ->setMaxObjectNum($mysqlConfig['max_object_num'])
                    ->setMinObjectNum($mysqlConfig['min_object_num'])
                    ->setGetObjectTimeout($mysqlConfig['get_object_timeout']);
            }

            $redisConfig = Config::get('redis.default');
            if (!empty($redisConfig)) {
                PoolManager::getInstance()->register($redisConfig['class'], $redisConfig['pool_size'])
                    ->setIntervalCheckTime($redisConfig['interval_check_time'])
                    ->setMaxIdleTime($redisConfig['max_idle_time'])
                    ->setMaxObjectNum($redisConfig['max_object_num'])
                    ->setMinObjectNum($redisConfig['min_object_num'])
                    ->setGetObjectTimeout($redisConfig['get_object_timeout']);
            }

            $rabbitmqConfig = Config::get('celery.default');
            if (!empty($rabbitmqConfig)) {
                PoolManager::getInstance()->register($rabbitmqConfig['class'], $rabbitmqConfig['pool_size'])
                    ->setIntervalCheckTime($rabbitmqConfig['interval_check_time'])
                    ->setMaxIdleTime($rabbitmqConfig['max_idle_time'])
                    ->setMaxObjectNum($rabbitmqConfig['max_object_num'])
                    ->setMinObjectNum($rabbitmqConfig['min_object_num'])
                    ->setGetObjectTimeout($rabbitmqConfig['get_object_timeout']);
            }
        }
    }

    public static function workerStop($worker_id)
    {
        if (($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0) {
            $mysqlConfig = Config::get('mysql.default');
            if (!empty($mysqlConfig)) {
//                go(function () use ($mysqlConfig) {
//                    PoolManager::getInstance()->getPool($mysqlConfig['class'], $mysqlConfig['pool_size'])->gcObject(0);
//                });
            }

            $redisConfig = Config::get('redis.default');
            if (!empty($redisConfig)) {
//                go(function () use ($redisConfig) {
//                    PoolManager::getInstance()->getPool($redisConfig['class'], $redisConfig['pool_size'])->gcObject(0);
//                });
            }

            $rabbitmqConfig = Config::get('celery.default');
            if (!empty($rabbitmqConfig)) {
//                go(function () use ($rabbitmqConfig) {
//                    PoolManager::getInstance()->getPool($rabbitmqConfig['class'], $rabbitmqConfig['pool_size'])->gcObject(0);
//                });
            }
        }
    }
}
<?php
namespace App\Providers;

use App\Utils\CeleryMqPool;
use App\Utils\MysqlPool;
use App\Utils\RedisPool;
use Dtsf\Core\Config;
use Dtsf\Core\Log;
use Dtsf\Core\Singleton;
use EasySwoole\Component\Pool\PoolManager;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-28
 * Time: 下午5:36
 */
class DtsfInitProvider
{
    use Singleton;

    public function workerStart($worker_id)
    {
        if (($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0) {
            $mysqlConfig = Config::get('mysql.default');
            if (!empty($mysqlConfig)) {
                PoolManager::getInstance()->register($mysqlConfig['class'])
                    ->setIntervalCheckTime($mysqlConfig['interval_check_time'])
                    ->setMaxIdleTime($mysqlConfig['max_idle_time'])
                    ->setMaxObjectNum($mysqlConfig['max_object_num'])
                    ->setMinObjectNum($mysqlConfig['min_object_num'])
                    ->setGetObjectTimeout($mysqlConfig['get_object_timeout']);
            }

            $redisConfig = Config::get('redis.default');
            if (!empty($redisConfig)) {
                PoolManager::getInstance()->register($redisConfig['class'])
                    ->setIntervalCheckTime($redisConfig['interval_check_time'])
                    ->setMaxIdleTime($redisConfig['max_idle_time'])
                    ->setMaxObjectNum($redisConfig['max_object_num'])
                    ->setMinObjectNum($redisConfig['min_object_num'])
                    ->setGetObjectTimeout($redisConfig['get_object_timeout']);
            }

            $rabbitmqConfig = Config::get('celery.default');
            if (!empty($rabbitmqConfig)) {
                PoolManager::getInstance()->register($rabbitmqConfig['class'])
                    ->setIntervalCheckTime($rabbitmqConfig['interval_check_time'])
                    ->setMaxIdleTime($rabbitmqConfig['max_idle_time'])
                    ->setMaxObjectNum($rabbitmqConfig['max_object_num'])
                    ->setMinObjectNum($rabbitmqConfig['min_object_num'])
                    ->setGetObjectTimeout($rabbitmqConfig['get_object_timeout']);

//                PoolManager::getInstance()->getPool($rabbitmqConfig['class'])->preLoad($rabbitmqConfig['min_object_num']);
            }
        }

        if (Config::get('env') == 'testing') {
            $this->debugPoolInfo();
            $this->debugCoroutineInfo();
        }
    }

    public function workerStop($worker_id)
    {
//        if (($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0) {
//            $mysqlConfig = Config::get('mysql.default');
//            if (!empty($mysqlConfig)) {
//                go(function () use ($mysqlConfig) {
//                    PoolManager::getInstance()->getPool($mysqlConfig['class'], $mysqlConfig['pool_size'])->gcObject(0);
//                });
//            }
//
//            $redisConfig = Config::get('redis.default');
//            if (!empty($redisConfig)) {
//                go(function () use ($redisConfig) {
//                    PoolManager::getInstance()->getPool($redisConfig['class'], $redisConfig['pool_size'])->gcObject(0);
//                });
//            }
//
//            $rabbitmqConfig = Config::get('celery.default');
//            if (!empty($rabbitmqConfig)) {
//                go(function () use ($rabbitmqConfig) {
//                    PoolManager::getInstance()->getPool($rabbitmqConfig['class'], $rabbitmqConfig['pool_size'])->gcObject(0);
//                });
//            }
//        }
    }

    public static function workerExit($worker_id)
    {
        //判断是不是worker进程
        if (($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0) {
            $rabbitmqConfig = Config::get('celery.default');
            if (!empty($rabbitmqConfig)) {
                go(function () use ($rabbitmqConfig) {
                    PoolManager::getInstance()->getPool($rabbitmqConfig['class'])->gcObject(-1);
                });
            }
        }
    }

    /**
     * debug链接池信息
     */
    private function debugPoolInfo()
    {
        swoole_timer_tick(2000, function () {
            Log::info([
                'CeleryMqPool' => "pid: {worker_id}---CeleryMqPool----" . json_encode(PoolManager::getInstance()->getPool(CeleryMqPool::class)->status()),
                'RedisPool' => "pid: {worker_id}---RedisPool----" . json_encode(PoolManager::getInstance()->getPool(RedisPool::class)->status()),
                'MysqlPool' => "pid: {worker_id}---MysqlPool----" . json_encode(PoolManager::getInstance()->getPool(MysqlPool::class)->status())],
                ['{worker_id}' => posix_getpid()], 'pool_num');
        });

        $totalMemory = memory_get_usage();
        $actualyMemory = memory_get_usage(true);
        $totalPeakMemory = memory_get_peak_usage();
        $actualyPeakMemory = memory_get_peak_usage(true);
        swoole_timer_tick(2000, function () use ($totalMemory, $actualyMemory, $totalPeakMemory, $actualyPeakMemory) {
            $totalMemory1 = memory_get_usage();
            $totalPeakMemory1 = memory_get_peak_usage();
            $actualyMemory1 = memory_get_usage(true);
            $actualyPeakMemory1 = memory_get_peak_usage(true);
            Log::info(
                "\r\n总内存上涨" . (($totalMemory1 - $totalMemory) / 1048576) .
                "M\r\n实际内存上涨" . (($actualyMemory1 - $actualyMemory) / 1048576) .
                "M\r\n总峰值内存上涨" . (($totalPeakMemory1 - $totalPeakMemory) / 1048576) .
                "M\r\n总峰值实际内存上涨" . (($actualyPeakMemory1 - $actualyPeakMemory) / 1048576) . 'M'
                , [], 'memory_use');
        });
    }

    /**
     * 输出协程信息
     */
    private function debugCoroutineInfo()
    {
        swoole_timer_tick(2000, function () {
            $croStat = \Swoole\Coroutine::stats();
            Log::info(
                "\r\n协程情况" . json_encode($croStat)
                , [], 'coroutine_info');
            if($croStat['coroutine_num'] == 1) {
                $coros = \Swoole\Coroutine::listCoroutines();
                foreach ($coros as $cid) {
                    Log::info(
                        "pid:{pid} 协程具体情况" . json_encode(\Swoole\Coroutine::getBackTrace($cid))
                        , ['{pid}'=>posix_getpid()], 'coroutine_info');
                }
            }

        });
    }
}
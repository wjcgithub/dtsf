<?php
namespace App\Providers;

use App\Exceptions\ExceptionLog;
use App\Utils\Pool\CeleryMqPool;
use App\Utils\Pool\SwooleMysqlPool;
use App\Utils\Pool\SwooleRedisPool;
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
            $this->memoryInfo();
            $this->debugCoroutineInfo();
        }
    }
    
    /**
     * worker stop
     * @param $worker_id
     */
    public function workerStop($worker_id){
        
    }
    
    /**
     * worker exit
     * @param $worker_id
     */
    public static function workerExit($worker_id)
    {
        //判断是不是worker进程
//        if (($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0) {
//            $rabbitmqConfig = Config::get('celery.default');
//            if (!empty($rabbitmqConfig)) {
//                go(function () use ($rabbitmqConfig) {
//                    PoolManager::getInstance()->getPool($rabbitmqConfig['class'])->gcObject(-1);
//                });
//            }
//        }
    }

    /**
     * debug链接池信息
     */
    private function debugPoolInfo()
    {
        swoole_timer_tick(2000, function () {
            Log::info([
                'CeleryMqPool' => "pid: {worker_id}---CeleryMqPool----" . json_encode(PoolManager::getInstance()->getPool(CeleryMqPool::class)->status()),
                'RedisPool' => "pid: {worker_id}---RedisPool----" . json_encode(PoolManager::getInstance()->getPool(SwooleRedisPool::class)->status()),
                'MysqlPool' => "pid: {worker_id}---MysqlPool----" . json_encode(PoolManager::getInstance()->getPool(SwooleMysqlPool::class)->status())],
                ['{worker_id}' => posix_getpid()], ExceptionLog::POOL_NUM);
        });
    }
    
    private function memoryInfo()
    {
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
                , [], ExceptionLog::MEMORY_USE);
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
                , [], ExceptionLog::CORO_INFO);
            if ($croStat['coroutine_num'] == 1) {
                $coros = \Swoole\Coroutine::listCoroutines();
                foreach ($coros as $cid) {
                    Log::info(
                        "pid:{pid} 协程具体情况" . json_encode(\Swoole\Coroutine::getBackTrace($cid))
                        , ['{pid}' => posix_getpid()], ExceptionLog::CORO_INFO);
                }
            }

        });
    }
}
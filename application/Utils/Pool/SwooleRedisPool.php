<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-29
 * Time: 下午6:10
 */

namespace App\Utils\Pool;


use App\Exceptions\ExceptionLog;
use Dtsf\Core\Config;
use Dtsf\Core\Log;
use EasySwoole\Component\Pool\AbstractPool;

class SwooleRedisPool extends AbstractPool
{
    /**
     * 请在此处返回一个redis链接实例
     * @return SwooleRedisObject
     */
    protected function createObject()
    {
        try {
            $config = Config::get('redis.db');
            $redis = new SwooleRedisObject();
            $res = $redis->connect($config);
            if ($res === false) {
                Log::error("Failed to connect redis server.", [], ExceptionLog::POOL_REDIS_LOG);
                throw new \RuntimeException('Failed to connect redis server.');
            }
            return $redis;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
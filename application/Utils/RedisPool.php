<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-29
 * Time: 下午6:10
 */

namespace App\Utils;


use Dtsf\Core\Config;
use Dtsf\Core\Log;
use EasySwoole\Component\Pool\AbstractPool;

class RedisPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return MysqlObject
     */
    protected function createObject()
    {
        try {
            $config = Config::get('redis.db');
            $redis = new RedisObject();
            $res = $redis->connect($config);
            if ($res === false) {
                Log::error("Failed to connect redis server.");
                throw new \RuntimeException('Failed to connect redis server.');
            }
            return $redis;
        } catch (\Throwable $e) {
            return null;
        }
    }
    
    public function getLength()
    {
        return $this->chan->length();
    }
}
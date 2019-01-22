<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-15
 * Time: 下午4:55
 */

namespace App\Utils;


use EasySwoole\Component\Pool\PoolObjectInterface;

class CeleryMqObject extends \Celery implements PoolObjectInterface
{
    function gc()
    {
        $this->disconnect();
    }

    function objectRestore()
    {
        // 重置为初始状态
    }

    /**
     * 每个链接使用之前 都会调用此方法 请返回 true / false
     * 返回false时PoolManager会回收该链接 并重新进入获取链接流程
     * @return bool 返回 true 表示该链接可用 false 表示该链接已不可用 需要回收
     */
    function beforeUse(): bool
    {
        // 此处可以进行链接是否断线的判断 使用不同的数据库操作类时可以根据自己情况修改
//        return $this->getRedis()->connected;
        return true;
    }
}
<?php
namespace App\Utils\Pool;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-29
 * Time: 下午6:04
 */
use EasySwoole\Component\Pool\PoolObjectInterface;
use EasySwoole\Mysqli\Mysqli;

class SwooleMysqlObject extends Mysqli implements PoolObjectInterface
{
    function gc()
    {
        // 重置为初始状态
        $this->resetDbStatus();
        // 关闭数据库连接
        $this->disconnect();
    }
    
    function objectRestore()
    {
        // 重置为初始状态
        $this->resetDbStatus();
    }
    
    /**
     * 每个链接使用之前 都会调用此方法 请返回 true / false
     * 返回false时PoolManager会回收该链接 并重新进入获取链接流程
     * @return bool 返回 true 表示该链接可用 false 表示该链接已不可用 需要回收
     */
    function beforeUse(): bool
    {
        try {
            return $this->getMysqlClient()->connected;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
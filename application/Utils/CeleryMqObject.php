<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-15
 * Time: 下午4:55
 */

namespace App\Utils;


use Dtsf\Core\Log;
use Dtsf\Core\WorkerApp;
use EasySwoole\Component\Pool\PoolObjectInterface;

class CeleryMqObject extends \Celery implements PoolObjectInterface
{
    public $objectName = '';

    public function gc()
    {
        Log::info("obj {obj} of worker {worker_id} start execting celeryMq gc, and current app status is {status}."
            , [
                '{obj}' => $this->objectName,
                '{worker_id}' => posix_getppid(),
                '{status}' => WorkerApp::getInstance()->serverStatus
            ]
            , WorkerApp::getInstance()->debugDirName);
        $this->recycleLastAck();
        $this->disconnect();
    }

    public function objectRestore()
    {
        // 重置为初始状态
    }

    /**
     * 每个链接使用之前 都会调用此方法 请返回 true / false
     * 返回false时PoolManager会回收该链接 并重新进入获取链接流程
     * @return bool 返回 true 表示该链接可用 false 表示该链接已不可用 需要回收
     */
    public function beforeUse(): bool
    {
        return $this->getBrokerConnectStatus();
    }
}
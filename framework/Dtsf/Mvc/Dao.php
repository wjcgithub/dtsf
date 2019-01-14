<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:04
 */

namespace Dtsf\Mvc;


use Dtsf\Core\Config;
use Dtsf\Core\Log;
use Dtsf\Coroutine\Coroutine;
use EasySwoole\Component\Pool\PoolManager;

class Dao
{
    protected $storage;
    protected $waitPoolTime = 1;

    /**
     * 获取一个链接类型的db实例
     *
     * @param string $dbTag
     * @return mixed
     */
    public function getDb()
    {
        $coId = Coroutine::getId();
        if (empty($this->storage[$coId])) {
            //不同协程不能复用mysql链接, 所以通过协程id进行资源隔离
            //达到同一个协程只用一个mysql链接, 不同协程用不同的mysql链接
            $this->storage[$coId] = PoolManager::getInstance()->getPool(Config::get($this->daoType . '.' . $this->connection . '.class'))
                ->getObj(Config::get($this->daoType . '.' . $this->connection . '.pool_get_timeout'));
            if (empty($this->storage[$coId])) {
                Log::emergency($this->daoType . '.' . $this->connection . "链接不够用了-再次申请after{$this->waitPoolTime}s", [], 'dbpool');
                \Swoole\Coroutine::sleep($this->waitPoolTime++);
                $this->getDb();
            }else{
                defer(function () {
                    $this->recycle();
                });
            }
        }

        return $this->storage[$coId];
    }

    /**
     * @throws \Exception
     * @desc mysql资源回收到连接池
     */
    public function recycle()
    {
//        print_r("current pool count: " . count($this->storage) . "\r\n");
        $coId = Coroutine::getId();
        if (!empty($this->storage[$coId])) {
            $object = $this->storage[$coId];
            unset($this->storage[$coId]);
            $this->waitPoolTime = 1;
//            print_r("Dao Coroutine" . \Swoole\Coroutine::getuid() . "-- defer event\r\n");
            $pool = PoolManager::getInstance()->getPool(Config::get($this->daoType . '.' . $this->connection . '.class'));
//            print_r('release before:' . $pool->getLength() . "\r\n");
            $pool->recycleObj($object);
//            print_r('release after:' . $pool->getLength() . "\r\n");
        }
    }
}
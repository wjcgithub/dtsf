<?php
namespace App\Utils;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-29
 * Time: 下午6:04
 */
use EasySwoole\Component\Pool\PoolObjectInterface;
use ParagonIE\EasyDB\Factory;
use PDO;
use PDOException;

class MysqlPdoObject extends Factory implements PoolObjectInterface
{
    function gc()
    {
        // 重置为初始状态
        $pdo = $this->getPdo();
        $pdo = null;
    }

    function objectRestore()
    {
        // 重置为初始状态
//        $this->resetDbStatus();
    }

    /**
     * 每个链接使用之前 都会调用此方法 请返回 true / false
     * 返回false时PoolManager会回收该链接 并重新进入获取链接流程
     * @return bool 返回 true 表示该链接可用 false 表示该链接已不可用 需要回收
     */
    function beforeUse(): bool
    {
        // 此处可以进行链接是否断线的判断 使用不同的数据库操作类时可以根据自己情况修改
        return $this->pdo_ping();
    }

    /**
     * 检查连接是否可用
     * @param  Link $dbconn 数据库连接
     * @return Boolean
     */
    function pdo_ping(){
        try{
            $this->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (PDOException $e) {
            if(strpos($e->getMessage(), 'MySQL server has gone away')!==false){
                return false;
            }
        }
        return true;
    }
}
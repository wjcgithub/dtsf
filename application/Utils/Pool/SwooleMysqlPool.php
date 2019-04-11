<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-29
 * Time: 下午6:10
 */

namespace App\Utils\Pool;


use Dtsf\Core\Config;
use EasySwoole\Component\Pool\AbstractPool;

class SwooleMysqlPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return SwooleMysqlObject
     */
    protected function createObject()
    {
        try {
            $conf = Config::get('mysql.default.master');
            $dbConf = new \EasySwoole\Mysqli\Config($conf);
            $mysqlObj = new SwooleMysqlObject($dbConf);
            $mysqlObj->connect();
            return $mysqlObj;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
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

class MysqlPdoPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return MysqlPdoObject
     */
    protected function createObject()
    {
        try {
            $conf = Config::get('mysql.default.master');
            return MysqlPdoObject::create("mysql:host={$conf['host']};dbname={$conf['database']}", $conf['user'], $conf['password'], []);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
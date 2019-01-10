<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-29
 * Time: 下午6:10
 */

namespace App\Utils;


use Dtsf\Core\Config;
use EasySwoole\Component\Pool\AbstractPool;

class MysqlPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return MysqlObject
     */
    protected function createObject()
    {
        $conf = Config::get('mysql.default.master');
        $dbConf = new \EasySwoole\Mysqli\Config($conf);
        return new MysqlObject($dbConf);
    }

    public function getLength()
    {
        return $this->chan->length();
    }
}
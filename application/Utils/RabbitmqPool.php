<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-15
 * Time: 下午4:55
 */

namespace App\Utils;


use Dtsf\Core\Config;
use EasySwoole\Component\Pool\AbstractPool;

class RabbitmqPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return MysqlObject
     */
    protected function createObject()
    {
        $config = Config::get('rabbitmq.default');
        return new \Celery(
            $config['host'],
            $config['uname'],
            $config['pwd'],
            $config['vhost'],
            $config['exchange'],
            '',
            $config['port'],
            'pecl'
        );
    }

    public function getLength()
    {
        return $this->chan->length();
    }
}
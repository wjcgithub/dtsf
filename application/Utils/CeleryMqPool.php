<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-15
 * Time: 下午4:55
 */

namespace App\Utils;

use App\Utils\CeleryMqObject;
use Dtsf\Core\Config;
use EasySwoole\Component\Pool\AbstractPool;

class CeleryMqPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return MysqlObject
     */
    protected function createObject()
    {
        $config = Config::get('celery.default');
        $obj = new CeleryMqObject(
            $config['host'],
            $config['uname'],
            $config['pwd'],
            $config['vhost'],
            $config['exchange'],
            '',
            $config['port'],
            function ($m){echo "\r\n ack ok ". $m->body ." \r\n";},
            function ($m){echo "\r\n nack ok \r\n";},
            function ($m){echo "\r\n return error \r\n";},
            'php-amqplib'
//            'swoole'
//            'pecl'
        );
        $obj->objectName = uniqid();
        return $obj;
    }

    public function getLength()
    {
        return $this->chan->length();
    }
}
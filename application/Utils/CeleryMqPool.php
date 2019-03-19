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
use EasySwoole\Component\Pool\PoolObjectInterface;

class CeleryMqPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return MysqlObject
     */
    protected function createObject(): PoolObjectInterface
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
            [],
            [],
            [],
//            __NAMESPACE__.'\MqConfirm::ack',
//            __NAMESPACE__.'\MqConfirm::nack',
//            __NAMESPACE__.'\MqConfirm::returnMsg',
            'php-amqplib',
            false,
            $config['connection_timeout'],
            $config['read_write_timeout']
        );
        $obj->objectName = uniqid();
        return $obj;
    }

    public function getLength()
    {
        return $this->chan->length();
    }
}
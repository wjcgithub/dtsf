<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-15
 * Time: 下午4:55
 */

namespace App\Utils;

use Dtsf\Core\Config;
use Dtsf\Core\Log;
use EasySwoole\Component\Pool\AbstractPool;

class CeleryMqPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return MysqlObject
     */
    protected function createObject()
    {
        try {
            $config = Config::get('celery.default');
            $confirmObj = new MqConfirm();
            $obj = new CeleryMqObject(
                $config['host'],
                $config['uname'],
                $config['pwd'],
                $config['vhost'],
                $config['exchange'],
                'dtsf_celery',
                $config['port'],
//            [],
//            [],
//            [],
                [$confirmObj, 'ack'],
                [$confirmObj, 'nack'],
                [$confirmObj, 'returnMsg'],
                'php-amqplib',
                0,
                $config['connection_timeout'],
                $config['read_write_timeout'],
                false,
                [],
                null,
                $config['keepalive'],
                $config['heartbeat']
            );
            $obj->objectName = uniqid();
            return $obj;
        } catch (\Throwable $e) {
            return null;
        }
    }
    
    public function getLength()
    {
        return $this->chan->length();
    }
}
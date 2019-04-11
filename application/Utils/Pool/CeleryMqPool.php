<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-15
 * Time: 下午4:55
 */

namespace App\Utils\Pool;

use App\Utils\MqConfirm\MqConfirmHandler;
use Dtsf\Core\Config;
use EasySwoole\Component\Pool\AbstractPool;

class CeleryMqPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return CeleryMqObject | null
     */
    protected function createObject()
    {
        try {
            $config = Config::get('celery.default');
            $confirmObj = new MqConfirmHandler();
            $object = new CeleryMqObject(
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
            $object->objectName = uniqid();
            return $object;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
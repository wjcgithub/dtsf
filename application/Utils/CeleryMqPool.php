<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-15
 * Time: 下午4:55
 */

namespace App\Utils;

use App\Dao\MsgDao;
use Dtsf\Core\Config;
use Dtsf\Core\Log;
use EasySwoole\Component\Pool\AbstractPool;
use PhpAmqpLib\Message\AMQPMessage;

class CeleryMqPool extends AbstractPool
{
    private $mqConfirmLogDir = 'mq_confirm_log';

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
            [$this, 'ack'],
            [$this, 'nack'],
            [$this, 'returnMsg'],
            'php-amqplib'
        );
        $obj->objectName = uniqid();
        return $obj;
    }

    public function getLength()
    {
        return $this->chan->length();
    }

    public function ack(AMQPMessage $message)
    {
        MsgDao::getCoInstance()->update([
            'status' => 1,
        ], "msgid='{$message->get_properties()['reply_to']}'");
    }

    public function nack(AMQPMessage $message)
    {
        $msg = "\r\n nack ok " . $message->get_properties()['reply_to'] . " \r\n";
        Log::error($msg, [], $this->mqConfirmLogDir);
    }

    public function returnMsg($params)
    {
        $msg = "\r\n return" . json_encode($params) . " \r\n";
        Log::error($msg, [], $this->mqConfirmLogDir);
    }
}
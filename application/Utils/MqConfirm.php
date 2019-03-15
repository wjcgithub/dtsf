<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-3-14
 * Time: 下午4:05
 */

namespace App\Utils;


use App\Dao\MsgDao;
use Dtsf\Core\Log;
use PhpAmqpLib\Message\AMQPMessage;

class MqConfirm implements MqConfirmInterface
{
    static private $mqConfirmLogDir = 'mq_confirm_log';

    static public function ack(AMQPMessage $message)
    {
        go(function () use ($message) {
            MsgDao::getCoInstance()->update([
                'status' => 1,
            ], "msgid='{$message->get_properties()['reply_to']}'");
        });
    }

    static public function nack(AMQPMessage $message)
    {
        $msg = "\r\n nack ok " . $message->get_properties()['reply_to'] . " \r\n";
        Log::error($msg, [], self::$mqConfirmLogDir);
    }

    static public function returnMsg(array $params)
    {
        $msg = "\r\n return" . json_encode($params) . " \r\n";
        Log::error($msg, [], self::$mqConfirmLogDir);
    }
}
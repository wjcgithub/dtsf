<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-3-14
 * Time: 下午4:05
 */

namespace App\Utils;


use PhpAmqpLib\Message\AMQPMessage;

interface MqConfirmInterface
{
    public function ack(AMQPMessage $message);

    public function nack(AMQPMessage $message);

    public function returnMsg(array $params);
}
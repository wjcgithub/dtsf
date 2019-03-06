<?php
require_once('./vendor/autoload.php');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
try{
    Swoole\Runtime::enableCoroutine();
    $exchange = 'first';
    $connection = new AMQPStreamConnection('develop', 56729, 'guest', 'guest', 'first');
    $channel = $connection->channel();
//    $channel->set_ack_handler(
//        function (AMQPMessage $message) {
//            echo "Message acked with content " . $message->body . PHP_EOL;
//        }
//    );
//    $channel->set_nack_handler(
//        function (AMQPMessage $message) {
//            echo "Message nacked with content " . $message->body . PHP_EOL;
//        }
//    );
//    $channel->set_return_listener(
//        function ($replyCode, $replyText, $exchange, $routingKey, AMQPMessage $message) {
//            echo "Message returned with content " . $message->body . PHP_EOL;
//        }
//    );
    /*
     * bring the channel into publish confirm mode.
     * if you would call $ch->tx_select() befor or after you brought the channel into this mode
     * the next call to $ch->wait() would result in an exception as the publish confirm mode and transactions
     * are mutually exclusive
     */
//    $channel->confirm_select();
    /*
        name: $exchange
        type: fanout
        passive: false // don't check if an exchange with the same name exists
        durable: false // the exchange won't survive server restarts
        auto_delete: true //the exchange will be deleted once the channel is closed.
    */
//    $channel->exchange_declare($exchange, 'direct', false, false, true);
    $i = 1;
//    $message = new AMQPMessage($i, array('content_type' => 'text/plain'));
//    $channel->basic_publish($message, $exchange, null, true);
    /*
     * watching the amqp debug output you can see that the server will ack the message with delivery tag 1 and the
     * multiple flag probably set to false
     */
//    $channel->wait_for_pending_acks_returns();
//    while ($i <= 2) {
        go(function () use($i, $channel, $exchange){
            $message = new AMQPMessage($i++, array('content_type' => 'text/plain'));
            $res = $channel->basic_publish($message, $exchange, 'group62_vm_test_2');
            echo "\r\nsend {$i} over\r\n";
            var_dump($res);
//            $channel->wait_for_pending_acks_returns();
        });
        echo "\r\n111111111111\r\n";
//    }
    /*
     * you do not have to wait for pending acks after each message sent. in fact it will be much more efficient
     * to wait for as many messages to be acked as possible.
     */
    $channel->close();
    $connection->close();
}catch (\Exception $e) {
    print_r($e->getMessage());
}
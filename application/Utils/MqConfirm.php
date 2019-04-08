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
    private $msgidPool = [];
    private $mqConfirmLogDir = 'mq_confirm_log';
    public $last_access_time = 0;
    private $tick = 0;         //刷新缓冲区的句柄
    private $tickTime = 5000;  //刷新缓冲区的时间间隔
    private $cacheNum = 200;   //缓冲区大小
    
    public function __construct()
    {
        $this->tick = swoole_timer_tick($this->tickTime, function () {
            if (time() - 5 >= $this->last_access_time) {
                $this->flushMsgidPool();
            }
        });
    }
    
    public function __destruct()
    {
        if ($this->tick) {
            swoole_timer_clear($this->tick);
        }
    }
    
    public function ack(AMQPMessage $message)
    {
        array_push($this->msgidPool, strval($message->get_properties()['reply_to']));
        if (count($this->msgidPool) >= $this->cacheNum) {
            $this->flushToDb();
        }
        $this->last_access_time = time();
    }
    
    public function nack(AMQPMessage $message)
    {
        $msg = "\r\n nack ok " . $message->get_properties()['reply_to'] . " \r\n";
        Log::error($msg, [], $this->mqConfirmLogDir);
    }
    
    public function returnMsg(array $params)
    {
        $msg = "\r\n return" . json_encode($params) . " \r\n";
        Log::error($msg, [], $this->mqConfirmLogDir);
    }
    
    /**
     * 刷新数据到数据库
     */
    private function flushToDb()
    {
        go(function () {
            $msgStr = '';
            foreach ($this->msgidPool as $value) {
                $msgStr .= "'" . $value . "',";
            }
            $msgStr = rtrim($msgStr, ',');
            MsgDao::getCoInstance()->update([
                'status' => 1,
            ], "msgid in (" . $msgStr . ")");
        });
        $this->msgidPool = [];
    }
    
    public function flushMsgidPool()
    {
        if (empty($this->msgidPool)) {
            return;
        }
        $this->flushToDb();
        $this->last_access_time = time();
    }
    
    public function getCacheLength()
    {
        return count($this->msgidPool);
    }
}
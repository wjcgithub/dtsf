<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:30
 */

namespace App\Controller;


use App\Dao\CeleryMqDao;
use App\Dao\RedisDefaultDao;
use App\Dao\UserDao;
use App\Service\ApiService;
use Dtsf\Mvc\Controller;
use Dtsf\Pool\ContextPool;
use Swoole\Coroutine;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class IndexController extends Controller
{
    public function index()
    {
        //通过context拿到$request
        $context = ContextPool::get();
        $request = $context->getRequest();
        return 'i am family by route' . json_encode($request->get);
    }

    public function test()
    {
        return 'i am test';
    }

    public function insertToDbAndCache()
    {
        $nameArr = ['zhangsan', 'lisi'];
        $array = [
            'name' => $nameArr[shuffle($nameArr)],
            'course' => str_shuffle('fjdlsakriepslfj'),
            'score' => rand(1, 100),
        ];



        echo 11111;

        go(function (){

            $cresult = CeleryMqDao::getInstance()->insert(
                time().random_int(1,100000),
                'vm_test_2.task.handler',
                ['payload' => '{"p":"{\"name\":\"\u5f20\u4e09\"}","c":"\/usr\/bin\/php \/home\/develop\/command.php","t":"2","tid":17}'],
                'group62_vm_test_2'
            );

////            var_dump(RedisDefaultDao::getInstance()->lpush('co_list', time()));
////            file_get_contents('/home/post.txt');
//            var_dump(ApiService::getInstance()->PostTask('', 'd97306f851fb12badd598083cfaf8cc9', '123456'));
//            $this->testpeclamqp();
            echo 2222222;
        });

        echo 333333333;

//        go(function (){
////            file_get_contents('/home/post.txt');
////            var_dump(RedisDefaultDao::getInstance()->lpush('co_list', time()));
////            var_dump(ApiService::getInstance()->PostTask('', 'd97306f851fb12badd598083cfaf8cc9', '123456'));
////            $this->testpeclamqp();
//
//            echo 444444;
//        });

        echo 55555555;

//        echo "into".PHP_EOL;
//        var_dump(UserDao::getInstance()->add($array));
//        echo "db->end" . PHP_EOL;
//        var_dump(RedisDefaultDao::getInstance()->lpush('co_list', time()));
//        Coroutine::sleep(1);
//        echo "redis->end" . PHP_EOL;
//        echo "\r\n 111 \r\n";
//                var_dump(RedisDefaultDao::getInstance()->lpush('co_list', time()));
//        var_dump(UserDao::getInstance()->add($array));
//        var_dump(ApiService::getInstance()->PostTask('', 'd97306f851fb12badd598083cfaf8cc9', '123456'));
//        echo "\r\n 222 \r\n";
//        Coroutine::sleep(5);
        echo "celery->end" . PHP_EOL;

        return "yes";
    }

    public function insertToRedis()
    {
        $val = $this->data['value'];
        $ins = RedisDefaultDao::getInstance();
        $ins->val = $val;
//        Coroutine::sleep($val);
        var_dump($ins->lpush('co_list', $ins->val));
        return "redis->end {$ins->val}" . PHP_EOL;
    }

    public function rawRabbitmq()
    {
        $exchange = 'router';
        $queue = 'msgs';
        $connection = new AMQPStreamConnection('develop', 56729, 'guest', 'guest', 'first');
        $channel = $connection->channel();
        /*
            The following code is the same both in the consumer and the producer.
            In this way we are sure we always have a queue to consume from and an
                exchange where to publish messages.
        */
        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $channel->queue_declare($queue, false, true, false, false);
        /*
            name: $exchange
            type: direct
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        $channel->queue_bind($queue, $exchange);
        $messageBody = 'this is test';
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange);
        $channel->close();
        $connection->close();
    }

    public function testpeclamqp()
    {
        //配置信息
        $conn_args = array(
            'host' => 'develop',
            'port' => '56729',
            'login' => 'guest',
            'password' => 'guest',
            'vhost'=>'first'
        );
        $e_name = 'first'; //交换机名
//$q_name = 'q_linvo'; //无需队列名
        $k_route = 'amqp_test'; //路由key

//创建连接和channel
        $conn = new \AMQPConnection($conn_args);
        if (!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }
        $channel = new \AMQPChannel($conn);



//创建交换机对象
        $ex = new \AMQPExchange($channel);
        $ex->setName($e_name);
        date_default_timezone_set("Asia/Shanghai");
//发送消息
//$channel->startTransaction(); //开始事务
        for($i=0; $i<5; ++$i){
//            sleep(1);//休眠1秒
            //消息内容
            $message = "TEST MESSAGE!".date("h:i:sa");
//            echo "Send Message:".$ex->publish($message, $k_route)."\n";
            $ex->publish($message, $k_route);
        }
//$channel->commitTransaction(); //提交事务

        $conn->disconnect();
    }

}
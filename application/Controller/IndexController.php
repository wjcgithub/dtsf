<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:30
 */

namespace App\Controller;


use App\Dao\RabbitMqDao;
use App\Dao\RedisDefaultDao;
use App\Dao\UserDao;
use Dtsf\Mvc\Controller;
use Dtsf\Pool\ContextPool;

class IndexController extends Controller
{
    public function index()
    {
        //通过context拿到$request
        $context = ContextPool::get();
        $request = $context->getRequest();
        return 'i am family by route'. json_encode($request->get);
    }

    public function test()
    {
        return 'i am test';
    }

    public function insertToDbAndCache()
    {
        $nameArr = ['zhangsan','lisi'];
        $array = [
            'name' => $nameArr[shuffle($nameArr)],
            'course' => str_shuffle('fjdlsakriepslfj'),
            'score' => rand(1,100),
        ];

        echo "into".PHP_EOL;
        var_dump(UserDao::getInstance()->add($array));
        echo "db->end".PHP_EOL;
        var_dump(RedisDefaultDao::getInstance()->lpush('co_list', time()));
        echo "redis->end".PHP_EOL;
        RabbitMqDao::getInstance()->insert(
            'vm_test_2.task.handler',
            ['payload'=>'{"p":"{\"name\":\"\u5f20\u4e09\"}","c":"http:\/\/dtq.test.xin.com\/test\/celery-handler","t":"1","tid":10}'],
            'group62_vm_test_2');
        echo "rabbitmq->end".PHP_EOL;
        return "yes";
    }
}
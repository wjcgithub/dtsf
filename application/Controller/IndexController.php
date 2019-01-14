<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:30
 */

namespace App\Controller;


use App\Dao\RedisDefaultDao;
use App\Dao\UserDao;
use Dtsf\Pool\ContextPool;

class IndexController
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

        UserDao::getInstance()->add($array);
//        RedisDefaultDao::getInstance()->lpush('co_list', time());
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-28
 * Time: 下午16:56
 */

namespace App\Controller;


use App\Dao\RedisDefaultDao;
use App\Service\RedisService;
use Dtsf\Mvc\Controller;
use Swoole\Coroutine;

class RedisController extends Controller
{
    public function get()
    {
        return RedisService::getInstance()->get($this->request->getQueryParam('key'));
    }

    /**
     * @return string
     */
    public function insertToRedis()
    {
        $result = '';
        $redis = RedisDefaultDao::getInstance();
        $redis->setex('key1', 300, 'test-test-1');
        $redis->setex('ket2', 300, 'test-test-2');

        $result .= "redis get key1<br>" . PHP_EOL;
        \Dtsf\Coroutine\Coroutine::create(function () use ($redis, &$result){
            $val1 = $redis->get('key1');
            $result .= "redis key1 value: {$val1}<br>" . PHP_EOL;
        });

        $result .= "redis get key2<br>" . PHP_EOL;
        \Dtsf\Coroutine\Coroutine::create(function () use ($redis, &$result){
            $val2 = $redis->get('key1');
            $result .= "redis key2 value: {$val2}<br>" . PHP_EOL;
        });

        Coroutine::sleep(1);

        return "redis->end output <br> {$result}" . PHP_EOL;
    }
}
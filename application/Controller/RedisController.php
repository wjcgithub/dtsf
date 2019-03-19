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
use Co\Chan;
use Dtsf\Mvc\Controller;

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
        $chan = new \chan(2);
        $result = '';
        $redis = RedisDefaultDao::getInstance();
        $redis->setex('key1', 300, 'test-test-1');
        $redis->setex('ket2', 300, 'test-test-2');

        $result .= "redis get key1<br>" . PHP_EOL;
        \Dtsf\Coroutine\Coroutine::create(function () use ($redis, $chan){
            $val1 = $redis->get('key1');
            $s1 = "redis key1 value: {$val1}<br>" . PHP_EOL;
            $chan->push($s1);

        });

        $result .= "redis get key2<br>" . PHP_EOL;
        \Dtsf\Coroutine\Coroutine::create(function () use ($redis, $chan){
            $val2 = $redis->get('key1');
            $s2 = "redis key2 value: {$val2}<br>" . PHP_EOL;
            $chan->push($s2);
        });

        for ($i=0; $i<2; $i++) {
            $result.=$chan->pop();
        }

        return "redis->end output <br> {$result}" . PHP_EOL;
    }
}
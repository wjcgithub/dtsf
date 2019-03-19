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
        return 'i am dtsf by route' . json_encode($this->request);
    }
}
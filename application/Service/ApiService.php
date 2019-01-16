<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-16
 * Time: 下午5:34
 */

namespace App\Service;

use App\Dao\RabbitMqDao;
use Dtsf\Core\Log;

class ApiService extends AbstractService
{
    public function PostTask()
    {
        $rest = RabbitMqDao::getInstance()->insert(
            'vm_test_2.task.handler',
            ['payload'=>'{"p":"{\"name\":\"\u5f20\u4e09\"}","c":"http:\/\/dtq.test.xin.com\/test\/celery-handler","t":"1","tid":10}'],
            'group62_vm_test_2');
//        static $i=0;
//        $res = $this->serv->taskCo([['celery' => $i]], 1);
//        if (empty($res[0])){
//            Log::error("erro {$i}", [], 'rabbit_error');
//        }
//        $i++;
        return $rest;
    }
}
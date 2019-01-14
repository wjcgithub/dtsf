<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-28
 * Time: 下午16:56
 */

namespace App\Controller;


use App\Service\RedisService;
use Dtsf\Mvc\Controller;

class RedisController extends Controller
{
    public function get()
    {
//        if (empty($this->request->getQueryParam('key'))) {
//            throw new \InvalidArgumentException('key 不能为空');
//        }
//        return RedisService::getInstance()->get($this->request->getQueryParam('key'));
        return RedisService::getInstance()->get($this->request->getQueryParam('key'));
    }
}
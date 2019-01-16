<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-16
 * Time: 下午5:47
 */

namespace App\Controller;


use App\Service\ApiService;

class ApiController
{
    public function PostTask()
    {
        ApiService::getInstance()->PostTask();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-16
 * Time: 下午5:47
 */

namespace App\Controller;


use App\Service\ApiService;
use Dtsf\Mvc\Controller;

class ApiController extends Controller
{
    public function PostTask()
    {
        return ApiService::getInstance()->PostTask();
    }
}
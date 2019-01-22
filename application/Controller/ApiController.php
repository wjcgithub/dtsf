<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-16
 * Time: 下午5:47
 */

namespace App\Controller;


use App\Controller\Validates\ApiValidate;
use App\Service\ApiService;
use Dtsf\Mvc\Controller;

class ApiController extends Controller
{

    public $a = 1;

    /**
     * @param ApiService $service
     * @return mixed
     */
    public function PostTask()
    {
        if(!ApiValidate::getRequestInstance()->PostTaskValidate($this->data)){
            return ApiValidate::getRequestInstance()->getError()->__toString();
        }
        return ApiService::getRequestInstance()->PostTask('',$this->data['messageno'],$this->data['messagebody']);
    }

    public function test()
    {
        return ApiService::getRequestInstance()->PostTask(2);

        $this->a = 100;
        echo "start" . $this->a;
        return "ok";
    }
}
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
    /**
     * @param ApiService $service
     * @return mixed
     */
    public function PostTask()
    {
        if (!ApiValidate::getCoInstance()->PostTaskValidate($this->data)) {
            return ApiValidate::getCoInstance()->getError()->__toString();
        }
        return ApiService::getCoInstance()->PostTask('', $this->data['messageno'], $this->data['messagebody']);
    }

    public function test()
    {
        return ApiService::getCoInstance()->PostTask(2);
    }
}
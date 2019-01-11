<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:38
 */

namespace App\Controller;


use App\Service\UserService;
use Dtsf\Mvc\Controller;

class UserController extends Controller
{
    public function user()
    {
        if (empty($this->request->getQueryParam('uid'))) {
            throw new \InvalidArgumentException('Uid 不能为空');
        }

        $result = UserService::getInstance()->getUserInfoByUid($this->request->getQueryParam('uid'));
        return json_encode($result);
    }

    public function add()
    {
        $array = [
            'name' => $this->request->getQueryParam('name'),
            'course' => $this->request->getQueryParam('course'),
            'score' => $this->request->getQueryParam('score'),
        ];

        return UserService::getInstance()->add($array);
    }

    public function update()
    {
        $array = [
            'name' => $this->request->getQueryParam('name'),
            'course' => $this->request->getQueryParam('course'),
            'score' => $this->request->getQueryParam('score'),
        ];

        $id = $this->request->getQueryParam('id');
        return UserService::getInstance()->updateById($array, $id);
    }

    public function delete()
    {
        $id = $this->request->getQueryParam('id');
        return UserService::getInstance()->deleteById($id);
    }

    public function list()
    {
        $result = UserService::getInstance()->getUserInfoList();
        echo "=================goto================";
        return json_encode($result);
    }
}
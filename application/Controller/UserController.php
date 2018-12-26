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
        if (empty($this->request->get['uid'])) {
            throw new \Exception('Uid 不能为空');
        }

        $result = UserService::getInstance()->getUserInfoByUid($this->request->get['uid']);
        return json_encode($result);
    }

    public function add()
    {
        $array = [
            'name' => $this->request->get['name'],
            'course' => $this->request->get['course'],
            'score' => $this->request->get['score'],
        ];

        return UserService::getInstance()->add($array);
    }

    public function update()
    {
        $array = [
            'name' => $this->request->get['name'],
            'course' => $this->request->get['course'],
            'score' => $this->request->get['score'],
        ];

        $id = $this->request->get['id'];
        return UserService::getInstance()->updateById($array, $id);
    }

    public function delete()
    {
        $id = $this->request->get['id'];
        return UserService::getInstance()->deleteById($id);
    }
}
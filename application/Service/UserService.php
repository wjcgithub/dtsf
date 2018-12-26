<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:34
 */

namespace App\Service;


use App\Dao\UserDao;
use Dtsf\Core\Singleton;

class UserService
{
    use Singleton;

    public function getUserInfoByUid($id)
    {
        return UserDao::getInstance()->fetchById();
    }

    public function getUserInfoList()
    {
        return UserDao::getInstance()->fetchAll();
    }

    public function add(array $array)
    {
        return UserDao::getInstance()->add($array);
    }

    public function updateById(array $array, $id)
    {
        return UserDao::getInstance()->update($array, "id={$id}");
    }

    public function deleteById($id)
    {
        return UserDao::getInstance()->delete("id={$id}");
    }
}
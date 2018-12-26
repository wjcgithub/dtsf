<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:07
 */

namespace App\Dao;


use Dtsf\Core\Singleton;
use Dtsf\Mvc\Dao;

class UserDao extends Dao
{
    use Singleton;

    public function __construct()
    {
        parent::__construct('App\Entity\UserEntity');
    }
}
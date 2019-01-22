<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-18
 * Time: 下午3:01
 */

namespace App\Dao;


use Dtsf\Core\Singleton;
use Dtsf\Mvc\DbDao;

class TasksDao extends DbDao
{
    use Singleton;

    public function __construct()
    {
        parent::__construct('App\Entity\TasksEntity');
    }
}
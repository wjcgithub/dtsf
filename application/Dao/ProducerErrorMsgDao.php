<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-18
 * Time: 下午4:26
 */

namespace App\Dao;


use Dtsf\Core\Singleton;
use Dtsf\Mvc\DbDao;

class ProducerErrorMsgDao extends DbDao
{
    use Singleton;

    public function __construct()
    {
        parent::__construct('App\Entity\ProducerErrorMsgEntity');
    }
}
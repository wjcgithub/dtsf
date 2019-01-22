<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-18
 * Time: 下午3:02
 */

namespace App\Entity;


use Dtsf\Mvc\Entity;

class TasksEntity extends Entity
{
    const CONNECTION = 'default';
    const TABLE_NAME = 'tasks';
    const PK_ID = 'id';
    //数据表字段
    public $id;
    public $taskname;
    public $masterid;
    public $mastername;
    public $tid;
    public $queueid;
    public $groupid;
    public $sys_type_id;
    public $type;
    public $ctime;
    public $utime;
    public $lastmasterid;
    public $status;
    public $callback_url;
    public $timeout;
    public $private_key;
}
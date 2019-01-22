<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-18
 * Time: 下午3:02
 */

namespace App\Entity;


use Dtsf\Mvc\Entity;

class QueueEntity extends Entity
{
    const CONNECTION = 'default';
    const TABLE_NAME = 'queues';
    const PK_ID = 'id';
    //数据表字段
    public $id;
    public $gid;
    public $masterid;
    public $name;
}
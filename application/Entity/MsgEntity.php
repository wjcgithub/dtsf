<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-18
 * Time: 下午3:02
 */

namespace App\Entity;


use Dtsf\Mvc\Entity;

class MsgEntity extends Entity
{
    const CONNECTION = 'default';
    const TABLE_NAME = 'msg';
    const PK_ID = 'msgid';
    //数据表字段
    public $msgid;
    public $payload;
    public $ctime;
    public $status;
}
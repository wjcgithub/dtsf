<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午5:55
 */

namespace App\Entity;


use Dtsf\Mvc\Entity;

class UserEntity extends Entity
{
    const CONNECTION = 'default';
    const TABLE_NAME = 'student';
    const PK_ID = 'id';
    //数据表字段
    public $id;
    public $name;
    public $course;
    public $score;
}
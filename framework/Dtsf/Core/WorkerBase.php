<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-3-18
 * Time: 上午11:53
 */

namespace Dtsf\Core;


class WorkerBase
{
    private $attributes = [];

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        return null;
    }
}
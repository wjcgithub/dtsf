<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午1:53
 */

namespace Dtsf\Core;

use Dtsf\Dtsf;
use SeasLog;

class Log
{
    /**
     * init seaslog
     */
    public static function init()
    {
        SeasLog::setBasePath(Dtsf::$applicationPath.DS.'Log');
    }

    /**
     * static to call
     *
     * @param $name
     * @param $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        forward_static_call_array(['SeasLog', $name], $arguments);
    }
}
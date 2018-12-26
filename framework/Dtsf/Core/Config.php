<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:09
 */

namespace Dtsf\Core;

use Dtsf\Dtsf;

class Config
{
    /**
     * 配置map
     *
     * @var
     */
    public static $configMap;

    /**
     * 读取配置文件, 默认是application/config/default.php
     */
    public static function load()
    {
        $configPath = Dtsf::$applicationPath . DS . 'Config';
        self::$configMap = require $configPath . DS . 'default.php';
    }

    /**
     * get config of default.php
     *
     * @param $key
     * @return null
     */
    public static function get($key, $def = null)
    {
        if (isset(self::$configMap[$key])) {
            return self::$configMap[$key];
        }
        return $def;
    }
}
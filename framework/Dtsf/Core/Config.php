<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:09
 */

namespace Dtsf\Core;

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
        $configPath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'config';
        self::$configMap = require $configPath . DIRECTORY_SEPARATOR . 'default.php';
    }

    public static function get($key)
    {
        if (isset(self::$configMap[$key])) {
            return self::$configMap[$key];
        }

        return null;
    }
}
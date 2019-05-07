<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:09
 */

namespace Dtsf\Core;

use Dtsf\Dtsf;
use Dtsf\Helper\Dir;

class Config
{
    /**
     * 配置map
     *
     * @var
     */
    public static $config;

    /**
     * 读取配置文件, 默认是application/config/default.php
     */
    public static function load()
    {
        $configPath = Dtsf::$applicationPath . DS . 'Config';
        self::$config = \Noodlehaus\Config::load($configPath . DS . 'server.php');
    }


    /**
     * @desc 读取配置，默认是application/config 下除default所有的php文件
     *          非default配置，可以热加载
     */
    public static function loadLazy()
    {
        $configPath = Dtsf::$applicationPath . DS . 'Config/';
        $configArr[] = $configPath.'mysql.php';
        $configArr[] = $configPath.'celery.php';
        $configArr[] = $configPath.'redis.php';
        $configArr[] = $configPath.'router.php';
        $config = new \Noodlehaus\Config($configPath);
        self::$config->merge($config);
    }

    /**
     * get config of default.php
     *
     * @param $key
     * @return null
     */
    public static function get($key, $def = '')
    {
        return self::$config->get($key, $def);
    }
}
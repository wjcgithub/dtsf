<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午2:53
 */

namespace Dtsf\Pool;
use Dtsf\Coroutine\Coroutine;


/**
 * Class Context
 * @package Dtsf\Pool
 * @desc context pool, 请求之间隔离, 请求之内任何地方可以存取
 */
class Context
{
    /**
     * @var array
     * @desc 上下文池
     */
    public static $pool = [];

    /**
     * @return mixed|null
     * @desc 可以在任意协程获取到context
     */
    public static function getContext()
    {
        $id = Coroutine::getPid();
        if (isset(self::$pool[$id])) {
            return self::$pool[$id];
        }

        return null;
    }

    /**
     * @desc 清除context
     */
    public static function clear()
    {
        $id = Coroutine::getPid();
        if (isset(self::$pool[$id])) {
            unset(self::$pool[$id]);
        }
    }

    /**
     * @param $context
     * @desc 设置context
     */
    public static function set($context)
    {
        $id = Coroutine::getPid();
        self::$pool[$id] = $context;
    }
}
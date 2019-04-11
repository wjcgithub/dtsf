<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-17
 * Time: 下午5:32
 */
if (! function_exists('app')) {
    /**
     * Assign high numeric IDs to a config item to force appending.
     *
     * @param  array  $array
     * @return array
     */
    function app(string $name)
    {
        return \Dtsf\Core\WorkerApp::getInstance()->getContainer()->get($name);
    }
}
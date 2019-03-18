<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-3-18
 * Time: 上午11:33
 */

namespace Dtsf\Core;


use Dtsf\Dtsf;

class MainService
{
    use Singleton;

    /**
     * 设置服务名称
     * @param $serverName
     */
    public function setProcessName($serverName)
    {
        if (PHP_OS != 'Darwin') {
            cli_set_process_title($serverName);
        }
    }

    /**
     * 初始化server并返回http实例
     *
     * @return \Swoole\Http\Server
     */
    public function serverStartBefore()
    {
        \Swoole\Runtime::enableCoroutine();
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        Dtsf::$rootPath = dirname(dirname(dirname(__DIR__)));
        Dtsf::$frameworkPath = Dtsf::$rootPath . DS . 'framework';
        Dtsf::$applicationPath = Dtsf::$rootPath . DS . 'application';
        //加载框架的基础配置
        Config::load();
        $timeZone = Config::get('time_zone', 'Asia/Shanghai');
        \date_default_timezone_set($timeZone);
        $http = new \Swoole\Http\Server(Config::get('host'), Config::get('port'));
        $http->set(Config::get('swoole_setting'));
        return $http;
    }

    /**
     * 初始化worker
     *
     * @param $serv
     */
    public function serverStart($serv)
    {
        $serverName = Config::get('server_name');
        $this->setProcessName($serverName);

        //日志初始化
        Log::init();
        file_put_contents(Dtsf::$rootPath . DS . 'bin' . DS . 'master.pid', $serv->master_pid);
        file_put_contents(Dtsf::$rootPath . DS . 'bin' . DS . 'manager.pid', $serv->manager_pid);
        Log::info("http server start! {host}: {port}, masterId:{masterId}, managerId: {managerId}", [
            '{host}' => Config::get('host'),
            '{port}' => Config::get('port'),
            '{masterId}' => $serv->master_pid,
            '{managerId}' => $serv->manager_pid,
        ], 'start');
    }

    /**
     * 服务退出
     */
    public function serverExit()
    {
        Log::info("http server shutdown", [], 'shutdown');
    }
}
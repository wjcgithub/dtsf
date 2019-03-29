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

    private $mainServer = null;
    private $hotReloadWatchDescriptor = null;

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
     * 初始化服务配置
     */
    public function initService()
    {
//        \Swoole\Runtime::enableCoroutine(true, SWOOLE_HOOK_ALL ^ SWOOLE_HOOK_STREAM_SELECT);
        \Swoole\Runtime::enableCoroutine();
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        Dtsf::$rootPath = dirname(dirname(dirname(__DIR__)));
        Dtsf::$frameworkPath = Dtsf::$rootPath . DS . 'framework';
        Dtsf::$applicationPath = Dtsf::$rootPath . DS . 'application';
        Dtsf::$applicationLogPath = Dtsf::$applicationPath . DS . 'Log';
        //加载框架的基础配置
        Config::load();
        $timeZone = Config::get('time_zone', 'Asia/Shanghai');
        \date_default_timezone_set($timeZone);
    }

    /**
     * 初始化server并返回http实例
     *
     * @return null|\Swoole\Http\Server
     */
    public function createMainService(): \Swoole\Http\Server
    {
        //SWOOLE_BASE
        $this->mainServer = new \Swoole\Http\Server(Config::get('host'), Config::get('port'));
        $serverConfig = Config::get('swoole_setting');
        $logBasePath = rtrim(Dtsf::$applicationLogPath, '/');
        if (!empty(Config::get('log_dir'))) {
            $logBasePath = rtrim(Config::get('log_dir'), '/');
        }
        $serverConfig['log_file'] = $logBasePath . DS . $serverConfig['log_file'];
        $this->mainServer->set($serverConfig);
        return $this->mainServer;
    }

    /**
     * 获取主服务名称
     *
     * @return null
     */
    public function getMainServer()
    {
        return $this->mainServer;
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

        if (Config::get('enableHotReload')) {
            $this->enableHotReload();
        }
    }

    /**
     * 服务退出
     */
    public function serverExit()
    {
        Log::info("http server shutdown", [], 'shutdown');
    }

    /**
     * 热加载
     */
    public function enableHotReload()
    {
        //创建一个inotify句柄
        $fd = inotify_init();

        $this->hotReloadWatchDescriptor = inotify_add_watch($fd, Dtsf::$applicationPath . '/',
            IN_MODIFY | IN_MOVED_FROM | IN_MOVED_TO | IN_CREATE | IN_DELETE | IN_DELETE_SELF | IN_ATTRIB);
        swoole_event_add($fd, function ($fd) {
            $events = inotify_read($fd);
            if ($events) {
                foreach ($events as $event) {
                    echo "inotify Event :" . var_export($event, 1) . "\n";
                }
            }
        });
    }
}
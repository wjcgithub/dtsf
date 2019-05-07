<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-3-18
 * Time: 上午11:33
 */

namespace Dtsf\Core;


use App\Exceptions\ExceptionLog;
use App\Utils\Common\Common;
use Dtsf\Dtsf;
use EasySwoole\Utility\File;

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
        ], ExceptionLog::SERVER_START);
        if (Config::get('enableHotReload')) {
            $this->enableHotReload();
        }
    }

    /**
     * 服务退出
     */
    public function serverExit()
    {
        Log::info("http server shutdown", [], ExceptionLog::SERVER_SHUTDOWN);
    }

    /**
     * 热加载
     */
    public function enableHotReload()
    {
        // 因为进程独立 且当前是自定义进程 全局变量只有该进程使用
        // 在确定不会造成污染的情况下 也可以合理使用全局变量
        global $lastReloadTime;
        $lastReloadTime = 0;
        
        //创建一个inotify句柄
        $notify = inotify_init();
        $files = File::scanDirectory(Dtsf::$applicationPath . '/Config');
        $list = array_merge($files['files'], $files['dirs']);
        inotify_add_watch($notify, Dtsf::$applicationPath . '/',
            IN_MODIFY | IN_MOVED_FROM | IN_MOVED_TO | IN_CREATE | IN_DELETE | IN_DELETE_SELF | IN_ATTRIB);
        foreach ($list as $item) {
            inotify_add_watch($notify, $item, IN_CREATE | IN_DELETE | IN_MODIFY);
        }
        
        swoole_event_add($notify, function ($notify) {
            global $lastReloadTime;
            $events = inotify_read($notify);
            if ($lastReloadTime < time() && !empty($events)) { // 限制1s内不能进行重复reload
                $lastReloadTime = time();
                echo $lastReloadTime;
                MainService::getInstance()->getMainServer()->reload();
            }
        });
    }
}
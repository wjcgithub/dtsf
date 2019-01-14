<?php
namespace Dtsf;

use App\Providers\DtsfInitProvider;
use Dtsf\Core\Config;
use Dtsf\Core\Log;
use Dtsf\Core\Route;
use Dtsf\Coroutine\Context;
use Dtsf\Coroutine\Coroutine;
use Dtsf\Pool\ContextPool;
use Swoole;

class Dtsf
{
    public static $rootPath;
    public static $frameworkPath;
    public static $applicationPath;

    /**
     * init framework
     */
    final public static function _init()
    {
        //init path
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        self::$rootPath = dirname(dirname(__DIR__));
        self::$frameworkPath = self::$rootPath . DS . 'framework';
        self::$applicationPath = self::$rootPath . DS . 'application';

        //加载框架的基础配置
        Config::load();
        $timeZone = Config::get('time_zone', 'Asia/Shanghai');
        \date_default_timezone_set($timeZone);
    }

    /**
     * start application
     */
    final public static function run()
    {
        try {
//            Swoole\Runtime::enableCoroutine();
            //启动前初始化
            self::_init();
            $http = new Swoole\Http\Server(Config::get('host'), Config::get('port'));
            $http->set(Config::get('swoole_setting'));

            $http->on('start', function (Swoole\Http\Server $serv) {
                //日志初始化
                Log::init();
                file_put_contents(self::$rootPath . DS . 'bin' . DS . 'master.pid', $serv->master_pid);
                file_put_contents(self::$rootPath . DS . 'bin' . DS . 'manager.pid', $serv->manager_pid);
                Log::info("http server start! {host}: {port}, masterId:{masterId}, managerId: {managerId}", [
                    '{host}' => Config::get('host'),
                    '{port}' => Config::get('port'),
                    '{masterId}' => $serv->master_pid,
                    '{managerId}' => $serv->manager_pid,
                ], 'start');
            });

            $http->on('shutdown', function () {
                //服务关闭，删除进程id
                unlink(self::$rootPath . 'DS' . 'bin' . DS . 'master.pid');
                unlink(self::$rootPath . 'DS' . 'bin' . DS . 'manager.pid');
                Log::info("http server shutdown", [], 'shutdown');
            });

            $http->on('workerStart', function (Swoole\Http\Server $serv, int $worker_id) {
                Log::info("worker {worker_id} started.", ['{worker_id}' => $worker_id], 'start');
                if (function_exists('opcache_reset')) {
                    //清除opcache缓存, swoole模式下建议关闭opcache
                    \opcache_reset();
                }
                try {
                    //加载配置，让此处加载的配置可热更新
                    Config::loadLazy();
                    //日志初始化
                    Log::init();
                    //给用户自己的权利去初始化
                    DtsfInitProvider::poolInit();
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    $serv->shutdown();
                } catch (\Throwable $throwable) {
                    Log::error($throwable->getMessage());
                    $serv->shutdown();
                }
            });

            $http->on('workerStop', function (Swoole\Http\Server $serv, int $worker_id) {
                Log::info("worker {worker_id} stoped.", ['{worker_id}' => $worker_id], 'stop');
            });

            //accept http request
            $http->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
                if ('/favicon.ico' === $request->server['path_info']) {
                    $response->end('');
                    return;
                }
                //初始化根协程ID
                Coroutine::setBaseId();
                //初始化上下文
                $context = new Context($request, $response);
                //存放到容器pool
                ContextPool::put($context);
                //协程退出,自动清空
                defer(function () {
                    //清空当前pool的上下文, 释放资源
                    ContextPool::release();
                });
                try {
                    //自动路由
                    $result = Route::dispatch();
                    $response->end($result);
                } catch (\Exception $e) { //程序异常
                    Log::exception($e);
                    $context->getResponse()->withStatus(500);
                } catch (\Error $e) { //程序错误，如fatal error
                    Log::exception($e);
                    $context->getResponse()->withStatus(500);
                } catch (\Throwable $e) {  //兜底
                    Log::exception($e);
                    $context->getResponse()->withStatus(500);
                }
            });
            $http->start();
        } catch (\Exception $e) {
            print_r($e);
        } catch (\Throwable $throwable) {
            print_r($throwable);
        }
    }
}
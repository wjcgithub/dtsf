<?php
namespace Dtsf;

use App\Dao\RabbitMqDao;
use App\Providers\DtsfInitProvider;
use App\Utils\CeleryMqPool;
use App\Utils\MysqlPool;
use App\Utils\RedisPool;
use Dtsf\Core\Config;
use Dtsf\Core\Log;
use Dtsf\Core\Route;
use Dtsf\Coroutine\Context;
use Dtsf\Coroutine\Coroutine;
use Dtsf\Pool\ContextPool;
use EasySwoole\Component\Pool\PoolManager;
use Swoole;

class Dtsf
{
    public static $rootPath;
    public static $frameworkPath;
    public static $applicationPath;
    public static $Di;

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
            Swoole\Runtime::enableCoroutine();
            //启动前初始化
            self::_init();
            $http = new Swoole\Http\Server(Config::get('host'), Config::get('port'));
            $http->set(Config::get('swoole_setting'));

            $http->on('start', function (Swoole\Http\Server $serv) {
                $serverName = Config::get('server_name');
                if (PHP_OS != 'Darwin') {
                    cli_set_process_title($serverName);
                }
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

            $http->on('managerStart', function (Swoole\Http\Server $serv) {
                $serverName = Config::get('server_name');
                if (PHP_OS != 'Darwin') {
                    cli_set_process_title("{$serverName}.manager");
                }
            });

            $http->on('shutdown', function () {
                //服务关闭，删除进程id
                unlink(self::$rootPath . DS . 'bin' . DS . 'master.pid');
                unlink(self::$rootPath . DS . 'bin' . DS . 'manager.pid');
                Log::info("http server shutdown", [], 'shutdown');
            });

            $http->on('workerStart', function (Swoole\Http\Server $serv, int $worker_id) {
                Log::info("worker {worker_id} started.", ['{worker_id}' => $worker_id], 'start');
                swoole_timer_tick(1000, function () use ($serv) {
                    Log::info(['t' => json_encode($serv->stats())], [], 'monitor');
                    Log::info([
                        'CeleryMqPool'=>"---CeleryMqPool----".json_encode(PoolManager::getInstance()->getPool(CeleryMqPool::class)->status()),
                        'RedisPool'=>"---RedisPool----".json_encode(PoolManager::getInstance()->getPool(RedisPool::class)->status()),
                        'MysqlPool'=>"---MysqlPool----".json_encode(PoolManager::getInstance()->getPool(MysqlPool::class)->status())],
                        [], 'pool_num');
                });
//                swoole_timer_tick(2000, function () {
//                    $coros = Swoole\Coroutine::listCoroutines();
//                    foreach($coros as $cid)
//                    {
////                        Log::info([
////                            'CeleryMqPool'=>"---CeleryMqPool----".json_encode(PoolManager::getInstance()->getPool(CeleryMqPool::class)->status()),
////                            'RedisPool'=>"---RedisPool----".json_encode(PoolManager::getInstance()->getPool(RedisPool::class)->status()),
////                            'MysqlPool'=>"---MysqlPool----".json_encode(PoolManager::getInstance()->getPool(MysqlPool::class)->status())],
////                            [], 'pool_num');
//                        var_dump(Swoole\Coroutine::getBackTrace($cid));
//                    }
//                });


                if (function_exists('opcache_reset')) {
                    //清除opcache缓存, swoole模式下建议关闭opcache
                    \opcache_reset();
                }
                try {
                    //加载配置，让此处加载的配置可热更新
                    Config::loadLazy();
                    //日志初始化
                    Log::init();
                    if (PHP_OS != 'Darwin') {
                        $name = Config::get('server_name');
                        if (($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0) {
                            $type = 'Worker';
                        } else {
                            $type = 'TaskWorker';
                        }
                        cli_set_process_title("{$name}.{$type}.{$worker_id}");
                    }
                    //给用户自己的权利去初始化
                    DtsfInitProvider::workerStart($worker_id);
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
                DtsfInitProvider::workerStop($worker_id);
            });

            /**
             * @todo test use
             */
//            $http->on('task', function (Swoole\Http\Server $serv, Swoole\Server\Task $task) {
//                $data = $task->data;
//                Swoole\Coroutine::sleep(0.3);
//                $task->finish($data);
//            });
//
//            $http->on('finish', function ($response) {
//
//            });


                //accept http request
            $http->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($http) {
                if ('/favicon.ico' === $request->server['path_info']) {
                    $response->end('');
                    return;
                }
//                Log::info($request->server['path_info'], [], 'access_log');
                //初始化根协程ID
                Coroutine::setBaseId();
                //初始化上下文
                $context = new Context($request, $response);
                $context->set('serv', $http);
                //存放到容器pool
                ContextPool::put($context);
                //协程退出,自动清空
                defer(function () {
                    //清空当前pool的上下文, 释放资源
                    ContextPool::release();
                });
                try {
                    //自动路由
                    $result = Route::getInstance()->dispatch();
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
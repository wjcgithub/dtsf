<?php
namespace Dtsf;

use Dtsf\Core\Config;
use Dtsf\Core\Log;
use Dtsf\Core\Route;
use Dtsf\Coroutine\Context;
use Dtsf\Coroutine\Coroutine;
use Dtsf\Mvc\Controller;
use Dtsf\Pool\ContextPool;
use Dtsf\Pool\MysqlPool;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request as EsRequest;
use EasySwoole\Http\Response as EsResponse;
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
        self::$frameworkPath = self::$rootPath.DS.'framework';
        self::$applicationPath = self::$rootPath.DS.'application';

        //init config
        Config::load();

        //init log
        Log::init();

        $timeZone = Config::get('time_zone', 'Asia/Shanghai');
        \date_default_timezone_set($timeZone);
    }

    final public static function run()
    {
        self::_init();
        $http = new Swoole\Http\Server(Config::get('host'), Config::get('port'));
        $http->set([
            'worker_num' => Config::get('worker_num')
        ]);

        $http->on('workerStart', function (\Swoole\Http\Server $serv, int $worker_id){
            if (function_exists('opcache_reset')){
                //清除opcache缓存, swoole模式下建议关闭opcache
                \opcache_reset();
            }

            try{
                $mysqlConfig = Config::get('mysql');
                if (!empty($mysqlConfig)){
                    //初始化mysql链接池
                    MysqlPool::getInstance($mysqlConfig);
                }
            }catch (\Exception $e) {
                Log::error($e->getMessage());
                print_r($e->getMessage());
                $serv->shutdown();
            }catch (\Throwable $throwable) {
                Log::error($throwable->getMessage());
                $serv->shutdown();
            }
        });

        $http->on('request', function ($request, $response){
            try{
                //初始化根协程ID
                $coId = Coroutine::setBaseId();
                //初始化上下文
                $request = new EsRequest($request);
//                $response = new EsResponse($response);
                $context = new Context($request, $response);
                //存放到容器pool
                ContextPool::set($context);
                //协程退出,自动清空
                defer(function () use ($coId){
                    //清空当前pool的上下文, 释放资源
                    ContextPool::clear($coId);
                });
                $result = Route::dispatch();
                $response->end($result);
            }catch (\Exception $exception) {
                Log::error($exception);
                $msg = 'msg '.$exception->getMessage().' file:'.$exception->getFile().' line:'.$exception->getLine().' trace:'.$exception->getTraceAsString();
                $response->end($msg);
            }catch (\Error $exception) {
                Log::error($exception);
                $msg = 'msg '.$exception->getMessage().' file:'.$exception->getFile().' line:'.$exception->getLine().' trace:'.$exception->getTraceAsString();
                $response->end($msg);
            }catch (\Throwable $exception) {
                Log::error($exception);
                $msg = 'msg '.$exception->getMessage().' file:'.$exception->getFile().' line:'.$exception->getLine().' trace:'.$exception->getTraceAsString();
                $response->end($msg);
            }
        });

        $http->start();
    }
}
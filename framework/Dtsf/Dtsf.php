<?php
namespace Dtsf;

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
        self::$frameworkPath = self::$rootPath.DS.'framework';
        self::$applicationPath = self::$rootPath.DS.'application';

        //init config
        Config::load();

        //init log
        Log::init();
    }

    final public static function run()
    {
        self::_init();
        $http = new Swoole\Http\Server(Config::get('host'), Config::get('port'));
        $http->set([
            'worker_num' => Config::get('worker_num')
        ]);

        $http->on('request', function ($request, $response){
            if ($request->server['path_info'] == '/favicon.ico'){
                $response->end('');
                return;
            }
            try{
                //初始化根协程ID
                $coId = Coroutine::setBaseId();
                //初始化上下文
                $context = new Context($request, $response);
                //存放到容器pool
                ContextPool::set($context);
                //协程退出,自动清空
                defer(function () use ($coId){
                    //清空当前pool的上下文, 释放资源
                    ContextPool::clear($coId);
                });
                $result = Route::dispatch($request->server['path_info']);
                $response->end($result);
            }catch (\Exception $e) {
                Log::alert($e->getMessage(), $e->getTrace());
                $response->end($e->getMessage());
            }catch (\Error $e) {
                Log::emergency($e->getMessage(), $e->getTrace());
                $response->status(500);
            }catch (\Throwable $e) {
                Log::emergency($e->getMessage(), $e->getTrace());
                $response->status(500);
            }
        });

        $http->start();
    }
}
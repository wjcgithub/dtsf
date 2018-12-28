<?php
namespace Dtsf;

use App\Dao\RedisCacheDao;
use App\Dao\RedisDbDao;
use App\Providers\DtsfInitProvider;
use Dtsf\Core\Config;
use Dtsf\Core\Log;
use Dtsf\Core\Route;
use Dtsf\Coroutine\Context;
use Dtsf\Coroutine\Coroutine;
use Dtsf\Pool\ContextPool;
use Dtsf\Pool\MysqlPool;
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
                DtsfInitProvider::poolInit();
            }catch (\Exception $e) {
                Log::error($e->getMessage());
                $serv->shutdown();
            }catch (\Throwable $throwable) {
                Log::error($throwable->getMessage());
                $serv->shutdown();
            }
        });

        $http->on('request', function ($request, $response){
            try{
                if ($request->server['path_info'] == '/favicon.ico'){
                    $response->end('');
                    return;
                }
                //初始化根协程ID
                $coId = Coroutine::setBaseId();
                print_r('request pool'.$coId.PHP_EOL);

                //初始化上下文
                $request = new EsRequest($request);
//                $response = new EsResponse($response);
                $context = new Context($request, $response);
                //存放到容器pool
                ContextPool::set($context);
                //协程退出,自动清空
                defer(function () use ($coId){
//                    print_r(MysqlPool::getInstance()->getLength().PHP_EOL);
//                    print_r(RedisCacheDao::getInstance()->getLength().PHP_EOL);
//                    print_r(RedisDbDao::getInstance()->getLength().PHP_EOL);
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
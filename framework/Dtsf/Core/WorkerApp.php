<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-17
 * Time: 下午5:35
 */

namespace Dtsf\Core;

use App\Exceptions\ExceptionLog;
use App\Providers\DtsfInitProvider;
use Dtsf\Coroutine\Context;
use Dtsf\Coroutine\Coroutine;
use Dtsf\Pool\ContextPool;

class WorkerApp extends WorkerBase
{
    use Singleton;

    const WORKERSTARTED = 1;
    const WORKERSTOPED = 2;
    const WORKEREXIT = 3;
    const WORKERLASTACK = 4;

    private function __construct()
    {
    }

    /**
     * 启动某个worker实例
     *
     * @param $server
     * @param $worker_id
     */
    public function workerStart($server, $worker_id)
    {
        if (function_exists('opcache_reset')) {
            //清除opcache缓存, swoole模式下建议关闭opcache
            \opcache_reset();
        }
        try {
            //加载配置，让此处加载的配置可热更新
            Config::loadLazy();
            Log::init();
            if (PHP_OS != 'Darwin') {
                $name = Config::get('server_name');
                if (($worker_id < Config::get('swoole_setting.worker_num')) && $worker_id >= 0) {
                    $type = 'Worker';
                } else {
                    $type = 'TaskWorker';
                }
                MainService::getInstance()->setProcessName("{$name}.{$type}.{$worker_id}");
            }
            $this->registerErrorHandler();
            DtsfInitProvider::getInstance()->workerStart($worker_id);
            WorkerApp::getInstance()->setWorkerStatus(WorkerApp::WORKERSTARTED);
            Log::info("worker {worker_id} started.", ['{worker_id}' => $worker_id], ExceptionLog::SERVER_START);
        }catch (\Throwable $throwable) {
            Log::error($throwable->getMessage(), [], ExceptionLog::SERVER_ERROR);
            $server->shutdown();
        }
    }
    
    /**
     * worker stop
     * @param $server
     * @param $worker_id
     */
    public function workerStop($server, $worker_id)
    {
        Log::info("worker {worker_id} stoped.", ['{worker_id}' => $worker_id], ExceptionLog::SERVER_STOP);
        $this->setWorkerStatus(WorkerApp::WORKERSTOPED);
        DtsfInitProvider::getInstance()->workerStop($worker_id);
    }
    
    /**
     * worker exit
     * @param $server
     * @param $worker_id
     */
    public function workerExit($server, $worker_id)
    {
        $this->setWorkerStatus(WorkerApp::WORKEREXIT);
        DtsfInitProvider::getInstance()->workerExit($worker_id);
    }
    
    /**
     * 异常处理
     */
    private function registerErrorHandler()
    {
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
        $userHandler = function($code, $message, $file = '', $line = 0, $context = array()){
            Log::error("{code}  {line}  {file}  {message}", [
                "{code}"=>$code,
                "{line}"=>$line,
                "{file}"=>$file,
                "{message}"=>$message,
            ], ExceptionLog::ERROR_HANDLER);
        };
        set_error_handler($userHandler);
        
        $func = function (){
            $error = error_get_last();
            if(!empty($error)){
                Log::error("{code}  {line}  {file}  {message}", [
                    "{code}"=>isset($error['code']) ? $error['code'] : '',
                    "{line}"=>isset($error['line']) ? $error['line'] : '',
                    "{file}"=>isset($error['file']) ? $error['file'] : '',
                    "{message}"=>isset($error['message']) ? $error['message'] : '',
                ], ExceptionLog::SHUTDOWN_ERROR);
            }
        };
        register_shutdown_function($func);
    }
    
    
    /**
     * 设置worker状态
     * @param int $status
     */
    public function setWorkerStatus(int $status)
    {
        $this->serverStatus = $status;
    }

    /**
     * 处理请求
     *
     * @param $http
     * @param $request
     * @param $response
     */
    public function performRequest($http, $request, $response)
    {
        if ('/favicon.ico' === $request->server['path_info']) {
            $response->end('');
            return;
        }
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
            Log::exception($e, ExceptionLog::SERVER_REQUEST);
            $context->getResponse()->withStatus(500);
        } catch (\Error $e) { //程序错误，如fatal error
            Log::exception($e, ExceptionLog::SERVER_REQUEST);
            $context->getResponse()->withStatus(500);
        } catch (\Throwable $e) {  //兜底
            Log::exception($e, ExceptionLog::SERVER_REQUEST);
            $context->getResponse()->withStatus(500);
        }
    }
}
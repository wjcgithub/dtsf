<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-17
 * Time: 下午5:35
 */

namespace Dtsf\Core;

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
        $this->debugDirName = 'debuginfo';
        $this->ackErrorDirName = 'mq_ack_error';
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
            //日志初始化
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
            //给用户自己的权利去初始化
            DtsfInitProvider::getInstance()->workerStart($worker_id);
            WorkerApp::getInstance()->setWorkerStatus(WorkerApp::WORKERSTARTED);
            Log::info("worker {worker_id} started.", ['{worker_id}' => $worker_id], 'start');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $server->shutdown();
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
            $server->shutdown();
        }
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
            Log::exception($e);
            $context->getResponse()->withStatus(500);
        } catch (\Error $e) { //程序错误，如fatal error
            Log::exception($e);
            $context->getResponse()->withStatus(500);
        } catch (\Throwable $e) {  //兜底
            Log::exception($e);
            $context->getResponse()->withStatus(500);
        }
    }
}
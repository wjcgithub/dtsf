<?php
namespace Dtsf;

use App\Providers\DtsfInitProvider;
use Dtsf\Core\Config;
use Dtsf\Core\Log;
use Dtsf\Core\MainService;
use Dtsf\Core\WorkerApp;
use Swoole;

class Dtsf
{
    public static $rootPath;
    public static $frameworkPath;
    public static $applicationPath;
    public static $applicationLogPath;

    /**
     * start application
     */
    final public static function run()
    {
        try {
            MainService::getInstance()->initService();
            $http = MainService::getInstance()->createMainService();
            $http->on('start', function (Swoole\Http\Server $serv) {
                MainService::getInstance()->serverStart($serv);
            });

            $http->on('managerStart', function (Swoole\Http\Server $serv) {
                $serverName = Config::get('server_name') . ".manager";
                MainService::getInstance()->setProcessName($serverName);
            });

            $http->on('shutdown', function () {
                MainService::getInstance()->serverExit();
            });

            $http->on('workerStart', function (Swoole\Http\Server $server, int $worker_id) {
                WorkerApp::getInstance()->workerStart($server, $worker_id);
            });

            $http->on('workerStop', function (Swoole\Http\Server $serv, int $worker_id) {
                WorkerApp::getInstance()->setWorkerStatus(WorkerApp::WORKERSTOPED);
                DtsfInitProvider::getInstance()->workerStop($worker_id);
                Log::info("worker {worker_id} stoped.", ['{worker_id}' => $worker_id], 'stop');
            });
            https://github.com/wjcgithub/dtsf   这个是我参考桶哥那个文章搞的又自己改造下, 目的想实现一个类似java里面那个mq的confirm机制, 符合我们这个100%消息投递场景, 现在还在开发, 你们那个框架有这个功能用吗, 有的话我就直接用了
            $http->on('workerExit', function (Swoole\Http\Server $serv, int $worker_id) {
//                if (WorkerApp::getInstance()->serverStatus !=)
                WorkerApp::getInstance()->setWorkerStatus(WorkerApp::WORKEREXIT);
                DtsfInitProvider::getInstance()->workerExit($worker_id);
                Log::info("worker {worker_id} exit.", ['{worker_id}' => $worker_id], 'stop');
            });

            $http->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($http) {
                WorkerApp::getInstance()->performRequest($http, $request, $response);
//
            });

            $http->start();
        } catch (\Exception $e) {
            Log::info("server exception, trace is." . $e->getTraceAsString(), [], 'error');
        } catch (\Throwable $e) {
            Log::info("server Throwable, trace is." . $e->getTraceAsString(), [], 'error');
        }
    }
}
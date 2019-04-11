<?php
namespace Dtsf;

use App\Exceptions\ExceptionLog;
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

            $http->on('workerStop', function (Swoole\Http\Server $server, int $worker_id) {
                WorkerApp::getInstance()->workerStop($server, $worker_id);
            });
            
            $http->on('workerExit', function (Swoole\Http\Server $server, int $worker_id) {
                WorkerApp::getInstance()->workerExit($server, $worker_id);
            });

            $http->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($http) {
                WorkerApp::getInstance()->performRequest($http, $request, $response);
            });

            $http->start();
        } catch (\Exception $e) {
            Log::info("server exception, trace is." . $e->getTraceAsString(), [], ExceptionLog::SERVER_ERROR);
        } catch (\Throwable $e) {
            Log::info("server Throwable, trace is." . $e->getTraceAsString(), [], ExceptionLog::SERVER_ERROR);
        }
    }
}
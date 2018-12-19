<?php
namespace Dtsf;

use Dtsf\Core\Config;
use Dtsf\Core\Log;
use Dtsf\Core\Route;
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
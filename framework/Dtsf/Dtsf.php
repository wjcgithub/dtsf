<?php
namespace Dtsf;

use Dtsf\Core\Config;
use Swoole;

class Dtsf
{
    final public static function run()
    {
        Config::load();
        $http = new Swoole\Http\Server(Config::get('host'), Config::get('port'));
        $http->set([
            'worker_num' => Config::get('worker_num')
        ]);

        $http->on('request', function ($request, $response){
           $response->end('hello, ddf is runing');
        });

        $http->start();
    }
}
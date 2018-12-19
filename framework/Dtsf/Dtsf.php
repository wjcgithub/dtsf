<?php
namespace Dtsf;

use Dtsf\Core\Config;
use Swoole;

class Dtsf
{
    final public static function run()
    {
        $http = new Swoole\Http\Server('0.0.0.0', 9501);
        $http->set([
            'worker_num' => 1
        ]);

        $http->on('request', function ($request, $response){
           $response->end('hello, ddf is runing');
        });

        $http->start();
    }
}
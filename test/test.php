<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-2-18
 * Time: 上午10:52
 */
while (1) {
    print_r(shell_exec('ab -n150 -c150 -p /home/post.txt  -T application/x-www-form-urlencoded localhost:9501/msg'));
    sleep(1);
}



//try {
//    Swoole\Runtime::enableCoroutine();
//    $http = new Swoole\Http\Server('localhost', 9009);
//    $http->set([
//        'worker_num' => 6,              //worker进程数量
//        'max_request' => 10000,
//        'max_coroutine' => 20000,
//        'reload_async' => true,
//        'log_file' => './swoole_error_log.log',
//        'max_wait_time' => 60,
//        'daemonize' => 0,               //是否开启守护进程
//    ]);
//
//    $http->on('workerStart', function (Swoole\Http\Server $serv, int $worker_id) {
//        swoole_timer_tick(2000, function () {
//            $croStat = \Swoole\Coroutine::stats();
//            if($croStat['coroutine_num'] == 1) {
//                $coros = \Swoole\Coroutine::listCoroutines();
//                foreach ($coros as $cid) {
////                    print_r(\Swoole\Coroutine::getBackTrace($cid));
//                }
//            }
//
//        });
//    });
//
//
//    //accept http request
//    $http->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($http) {
//        if ('/favicon.ico' === $request->server['path_info']) {
//            $response->end('');
//            return;
//        }
//        \Swoole\Coroutine::sleep(1);
//        echo "1111\r\n";
//    });
//    $http->start();
//} catch (\Exception $e) {
//    print_r($e);
//} catch (\Throwable $throwable) {
//    print_r($throwable);
//}

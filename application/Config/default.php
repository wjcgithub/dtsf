<?php
return [
    'server_name' => 'dtsf',
    'env' => 'proc',  //testing
    'host' => '0.0.0.0',
    'port' => 9501,
    'time_zone' => 'Asia/Shanghai',     //时区
    'swoole_setting' => [               //swoole配置
        'worker_num' => 1,              //worker进程数量
        'max_request' => 10000,
        'max_coroutine' => 20000,
        'reload_async' => true,
        'log_file' => '/tmp/swoole_error_log.log',
        'max_wait_time' => 60,
        'daemonize' => 0,               //是否开启守护进程
    ],
];
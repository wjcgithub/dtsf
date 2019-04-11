<?php
return [
    'server_name' => 'dtsf',
    'env' => 'proc',  //testing, proc
    'host' => '0.0.0.0',
    'port' => 9501,
    'time_zone' => 'Asia/Shanghai',     //时区
    'swoole_setting' => [               //swoole配置
        'worker_num' => 5,              //worker进程数量
        'max_request' => 10000,
        'max_coroutine' => 20000,
        'reload_async' => true,
//        'log_level' => 1,
        'log_file' => 'swoole_error_log.log',
        'max_wait_time' => 30,
        'daemonize' => 0, //是否开启守护进程
//        'heartbeat_check_interval' => 5,
//        'heartbeat_idle_time' => 10,
    ],
    'log_dir' => ''
];
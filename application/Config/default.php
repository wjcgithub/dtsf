<?php
return [
    'server_name' => 'dtsf',
    'env' => 'testing',
    'host' => '0.0.0.0',
    'port' => 9501,
    'time_zone' => 'Asia/Shanghai',     //时区
    'swoole_setting' => [               //swoole配置
        'worker_num' => 5,              //worker进程数量
        'max_request' => 10000,
        'max_coroutine' => 15000,
//        'task_worker_num' => 7,
//        'task_max_request' => 1000,
//        'task_enable_coroutine' => True,
        'reload_async' => true,
        'max_wait_time' => 60,
        'heartbeat_idle_time' => 600,
        'heartbeat_check_interval' => 60,
        'daemonize' => 0,               //是否开启守护进程
    ]
];
<?php
return [
    'env' => 'testing',
    'host' => '0.0.0.0',
    'port' => 9501,
    'time_zone' => 'Asia/Shanghai',     //时区
    'swoole_setting' => [               //swoole配置
        'worker_num' => 7,              //worker进程数量
        'max_request' => 1000,
        'heartbeat_idle_time' => 600,
        'heartbeat_check_interval' => 60,
        'daemonize' => 0,               //是否开启守护进程
    ]
];
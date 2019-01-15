<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-15
 * Time: 下午1:54
 */
return [
    'name' => "dtsf",
    'env' => 'testing',
    'time_zone' => 'Asia/Shanghai',     //时区
    'server' => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'swoole_setting' => [               //swoole配置
            'worker_num' => 8,//运行的 task_worker 进程数量
            'max_request' => 5000,// task_worker 完成该数量的请求后将退出，防止内存溢出
            'task_worker_num' => 8,//运行的 worker 进程数量
            'task_max_request' => 1000,// worker 完成该数量的请求后将退出，防止内存溢出
            'heartbeat_idle_time' => 600,
            'heartbeat_check_interval' => 60,
            'daemonize' => 0,               //是否开启守护进程
        ]
    ],
    'DI'=>[

    ]
];
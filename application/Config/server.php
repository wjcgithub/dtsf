<?php
return [
    "port" => 9501,
    "time_zone" => "Asia/Shanghai",
    "log_dir" => "",
    "swoole_setting" => [
        "daemonize" => false,
        "worker_num" => 5,
        "max_request" => 10000,
        "max_coroutine" => 20000,
        "reload_async" => true,
        "log_file" => "swoole_error_log.log",
        "max_wait_time" => 30,
    ],
    "server_name" => "dtsf",
    "env" => "proc",
    "host" => "0.0.0.0",
];

<?php
return [
    "swoole_setting" => [
        "worker_num" => 1,
        "max_request" => 100000,
        "max_coroutine" => 20000,
        "reload_async" => true,
        "log_file" => "swoole_error_log.log",
        "max_wait_time" => 20,
        "daemonize" => false,
    ],
    "server_name" => "dtsf",
    "env" => "proc",  //testing, proc
    "host" => "0.0.0.0",
    "port" => 9501,
    "time_zone" => "Asia/Shanghai",
    "log_dir" => "",
    "enableHotReload" => false,
];

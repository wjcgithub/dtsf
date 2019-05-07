<?php
return [
    "redis" => [
        "default" => [
            "options" => [
                "connect_timeout" => 1,
                "timeout" => 100,
                "reconnect" => 5,
            ],
            "interval_check_time" => 120000,
            "max_idle_time" => 30,
            "host" => "127.0.0.1",
            "get_object_timeout" => 0.2,
            "port" => 6379,
            "db" => 1,
            "class" => "\\App\\Utils\\Pool\\SwooleRedisPool",
            "max_object_num" => 50,
            "min_object_num" => 3,
        ],
        "db" => [
            "class" => "\\App\\Utils\\Pool\\SwooleRedisPool",
            "interval_check_time" => 120000,
            "max_idle_time" => 30,
            "max_object_num" => 50,
            "min_object_num" => 3,
            "get_object_timeout" => 0.2,
            "host" => "127.0.0.1",
            "port" => 6379,
            "db" => 1,
            "options" => [
                "connect_timeout" => 1,
                "timeout" => 100,
                "reconnect" => 5,
            ],
        ],
    ],
];

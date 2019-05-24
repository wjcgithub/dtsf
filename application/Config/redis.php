<?php
return [
    "redis" => [
        "default" => [
            "class" => "\\App\\Utils\\Pool\\SwooleRedisPool",
            "min_object_num" => 3,
            "host" => "10.70.120.126",
            "port" => 6379,
            "db" => 1,
            "options" => [
                "connect_timeout" => 1,
                "timeout" => 100,
                "reconnect" => 5,
            ],
            "interval_check_time" => 130000,
            "max_idle_time" => 30,
            "max_object_num" => 30,
            "get_object_timeout" => 0.2,
        ],
        "db" => [
            "options" => [
                "connect_timeout" => 1,
                "timeout" => 100,
                "reconnect" => 5,
            ],
            "class" => "\\App\\Utils\\Pool\\SwooleRedisPool",
            "interval_check_time" => 120000,
            "min_object_num" => 3,
            "get_object_timeout" => 0.2,
            "host" => "10.70.120.126",
            "port" => 6379,
            "db" => 1,
            "max_idle_time" => 30,
            "max_object_num" => 30,
        ],
    ],
];

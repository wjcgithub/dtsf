<?php
return [
    "celery" => [
        "default" => [
            "interval_check_time" => 120000,
            "port" => 56729,
            "pwd" => "guest",
            "read_write_timeout" => 2,
            "keepalive" => true,
            "class" => "\\App\\Utils\\Pool\\CeleryMqPool",
            "exchange" => "first",
            "max_object_num" => 50,
            "connection_timeout" => 3,
            "heartbeat" => 0,
            "vhost" => "first",
            "min_object_num" => 3,
            "get_object_timeout" => 0.2,
            "host" => "10.70.120.79",
            "uname" => "guest",
            "max_idle_time" => 30,
        ],
    ],
];

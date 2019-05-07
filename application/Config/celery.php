<?php
return [
    "celery" => [
        "default" => [
            "interval_check_time" => 120000,
            "max_idle_time" => 30,
            "max_object_num" => 50,
            "get_object_timeout" => 0.2,
            "uname" => "guest",
            "exchange" => "first",
            "heartbeat" => 0,
            "class" => "\\App\\Utils\\Pool\\CeleryMqPool",
            "host" => "develop",
            "pwd" => "guest",
            "connection_timeout" => 3,
            "read_write_timeout" => 2,
            "min_object_num" => 3,
            "vhost" => "first",
            "keepalive" => true,
            "port" => 56729,
        ],
    ],
];

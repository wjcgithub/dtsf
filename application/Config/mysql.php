<?php
return [
    "mysql" => [
        "default" => [
            "min_object_num" => 3,
            "get_object_timeout" => 0.2,
            "master" => [
                "charset" => "utf8mb4",
                "strict_type" => true,
                "host" => "127.0.0.1",
                "port" => 3306,
                "user" => "wjc",
                "password" => "123456",
                "database" => "xin_dtq",
                "timeout" => 100,
            ],
            "slave" => [
                0 => [
                    "database" => "xin_dtq",
                    "timeout" => 100,
                    "charset" => "utf8mb4",
                    "strict_type" => true,
                    "host" => "127.0.0.1",
                    "port" => 3306,
                    "user" => "wjc",
                    "password" => "123456",
                ],
                1 => [
                    "user" => "wjc",
                    "password" => "123456",
                    "database" => "xin_dtq",
                    "timeout" => 100,
                    "charset" => "utf8mb4",
                    "strict_type" => true,
                    "host" => "127.0.0.1",
                    "port" => 3306,
                ],
            ],
            "class" => "\\App\\Utils\\Pool\\SwooleMysqlPool",
            "interval_check_time" => 120000,
            "max_idle_time" => 30,
            "max_object_num" => 50,
        ],
    ],
];

<?php
return [
    "mysql" => [
        "default" => [
            "max_object_num" => 50,
            "min_object_num" => 3,
            "get_object_timeout" => 0.2,
            "master" => [
                "strict_type" => true,
                "host" => "127.0.0.1",
                "port" => 3306,
                "user" => "root",
                "password" => "brave",
                "database" => "xin_dtq",
                "timeout" => 100,
                "charset" => "utf8mb4",
            ],
            "slave" => [
                0 => [
                    "database" => "xin_dtq",
                    "timeout" => 100,
                    "charset" => "utf8mb4",
                    "strict_type" => true,
                    "host" => 12,
                    "port" => 3306,
                    "user" => "root",
                    "password" => "brave",
                ],
                1 => [
                    "database" => "xin_dtq",
                    "timeout" => 100,
                    "charset" => "utf8mb4",
                    "strict_type" => true,
                    "host" => 12,
                    "port" => 3306,
                    "user" => "root",
                    "password" => "brave",
                ],
            ],
            "class" => "\\App\\Utils\\Pool\\SwooleMysqlPool",
            "interval_check_time" => 120000,
            "max_idle_time" => 30,
        ],
    ],
];

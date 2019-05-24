<?php
return [
    "mysql" => [
        "default" => [
            "min_object_num" => 3,
            "get_object_timeout" => 0.2,
            "master" => [
                "charset" => "utf8mb4",
                "strict_type" => true,
                "host" => "10.70.120.126",
                "port" => 3306,
                "user" => "wjc",
                "password" => "wjc",
                "database" => "xin_dtq",
                "timeout" => 100,
            ],
            "slave" => [
                0 => [
                    "database" => "xin_dtq",
                    "timeout" => 100,
                    "charset" => "utf8mb4",
                    "strict_type" => true,
                    "host" => "10.70.120.126",
                    "port" => 3306,
                    "user" => "wjc",
                    "password" => "wjc",
                ],
                1 => [
                    "user" => "wjc",
                    "password" => "wjc",
                    "database" => "xin_dtq",
                    "timeout" => 100,
                    "charset" => "utf8mb4",
                    "strict_type" => true,
                    "host" => "10.70.120.126",
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

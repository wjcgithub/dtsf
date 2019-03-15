<?php
return [
    'mysql' => [
        'default' => [
            'class' => \App\Utils\MysqlPool::class,
            'interval_check_time' => 60*1000,
            'max_idle_time' => 10,
            'max_object_num' => 50,
            'min_object_num' => 5,
            'get_object_timeout' => 0.5,
            'master' => [
                'host' => '127.0.0.1',   //数据库ip
                'port' => 3306,          //数据库端口
                'user' => 'root',        //数据库用户名
                'password' => 'brave', //数据库密码
                'database' => 'xin_dtq',   //默认数据库名
                'timeout' => 100,       //数据库连接超时时间
                'charset' => 'utf8mb4', //默认字符集
                'strict_type' => true,  //ture，会自动表数字转为int类型
            ],
            'slave' => [
                [
                    'host' => '127.0.0.1',   //数据库ip
                    'port' => 3306,          //数据库端口
                    'user' => 'root',        //数据库用户名
                    'password' => 'brave', //数据库密码
                    'database' => 'xin_dtq',   //默认数据库名
                    'timeout' => 5,       //数据库连接超时时间
                    'charset' => 'utf8mb4', //默认字符集
                    'strict_type' => true,  //ture，会自动表数字转为int类型
                ],
                [
                    'host' => '127.0.0.1',   //数据库ip
                    'port' => 3306,          //数据库端口
                    'user' => 'root',        //数据库用户名
                    'password' => 'brave', //数据库密码
                    'database' => 'xin_dtq',   //默认数据库名
                    'timeout' => 5,       //数据库连接超时时间
                    'charset' => 'utf8mb4', //默认字符集
                    'strict_type' => true,  //ture，会自动表数字转为int类型
                ]
            ]
        ]
    ],
];
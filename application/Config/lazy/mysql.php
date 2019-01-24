<?php
return [
    'mysql' => [
        'default' => [
            'class' => \App\Utils\MysqlPool::class,
            'pool_size' => 60,     //连接池大小
            'interval_check_time' => 120*1000,
            'max_idle_time' => 15,
            'max_object_num' => 19,
            'min_object_num' => 4,
            'get_object_timeout' => 3, //当在此时间内未获得到一个连接，会立即返回。（表示所以的连接都已在使用中）
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
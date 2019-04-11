<?php
return [
    'redis' => [
        'default' => [
            'class' => \App\Utils\Pool\SwooleRedisPool::class,
            'host' => '127.0.0.1',
            'port' => 6379,
            'db' => '5',
            'interval_check_time' => 120*1000,
            'max_idle_time' => 30,
            'max_object_num' => 50,
            'min_object_num' => 5,
            'get_object_timeout' => 0.5,
            'options' => [
                'connect_timeout' => 1,
                'timeout' => 100,
                'reconnect' => 5
            ]
        ],
        'db' => [
            'class' => \App\Utils\Pool\SwooleRedisPool::class,
            'host' => '127.0.0.1',
            'port' => 6379,
            'db' => '5',
            'interval_check_time' => 120*1000,
            'max_idle_time' => 30,
            'max_object_num' => 50,
            'min_object_num' => 5,
            'get_object_timeout' => 0.5,
            'options' => [
                'connect_timeout' => 1,
                'timeout' => 100,
                'reconnect' => 5
            ]
        ],
    ],

];
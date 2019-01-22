<?php
return [
    'redis' => [
        'default' => [
            'class' => \App\Utils\RedisPool::class,
            'host' => '127.0.0.1',
            'port' => 6379,
            'db' => '5',
            'pool_size' => 15,
            'interval_check_time' => 120*1000,
            'max_idle_time' => 15,
            'max_object_num' => 20,
            'min_object_num' => 5,
            'get_object_timeout' => 1.5,
            'options' => [
                'connect_timeout' => 1,
                'timeout' => 100,
                'reconnect' => 5
            ]
        ],
        'db' => [
            'class' => \App\Utils\RedisPool::class,
            'host' => '127.0.0.1',
            'port' => 6379,
            'db' => '5',
            'pool_size' => 15,
            'interval_check_time' => 4*1000,
            'max_idle_time' => 15,
            'max_object_num' => 20,
            'min_object_num' => 5,
            'get_object_timeout' => 3,
            'options' => [
                'connect_timeout' => 1,
                'timeout' => 100,
                'reconnect' => 5
            ]
        ],
    ],

];
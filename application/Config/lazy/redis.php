<?php
return [
    'redis' => [
        'default' => [
            'class' => \App\Utils\RedisPool::class,
            'host' => 'develop',
            'port' => 6379,
            'pool_size' => 10,
            'pool_get_timeout' => 0.5,
            'options' => [
                'connect_timeout' => 1,
                'timeout' => 1,
                'reconnect' => 5
            ]
        ],
        'db' => [
            'class' => \App\Utils\RedisPool::class,
            'host' => '127.0.0.1',
            'port' => 6379,
            'pool_size' => 10,
            'pool_get_timeout' => 0.5,
            'options' => [
                'connect_timeout' => 1,
                'timeout' => 1,
                'reconnect' => 5
            ]
        ],
    ],

];
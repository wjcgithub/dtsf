<?php
return [
    'redis' => [
        'default' => [
            'host' => 'develop',
            'port' => 6379,
            'options' => [
                'connect_timeout' => 1,
                'timeout' => 1,
                'reconnect' => 5
            ],
            'pool_size' => 2,
            'pool_get_timeout' => 0.5
        ],
        'db' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'options' => [
                'connect_timeout' => 1,
                'timeout' => 1,
                'reconnect' => 5
            ],
            'pool_size' => 2,
            'pool_get_timeout' => 0.5
        ],
    ],

];
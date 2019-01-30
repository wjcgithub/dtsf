<?php
return [
    'celery'=>[
        'default' => [
            'class' => \App\Utils\CeleryMqPool::class,
            'pool_size' => 50,
            'interval_check_time' => 300*1000,
            'max_idle_time' => 150,
            'max_object_num' => 20,
            'min_object_num' => 5,
            'get_object_timeout' => 0.5,
            'host' => 'develop',
            'port' => 56729,
            'uname' => 'guest',
            'pwd' => 'guest',
            'vhost' => 'first',
            'exchange' => 'first',
        ]
    ]
];
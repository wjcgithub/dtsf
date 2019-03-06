<?php
return [
    'celery'=>[
        'default' => [
            'class' => \App\Utils\CeleryMqPool::class,
            'pool_size' => 150,
            'interval_check_time' => 300*1000, //检测mq链接的时间（s）
            'max_idle_time' => 120,  //最大空闲时间（s）
            'max_object_num' => 100,
            'min_object_num' => 3,
            'get_object_timeout' => 0.2,
            'host' => 'develop',
            'port' => 56729,
            'uname' => 'guest',
            'pwd' => 'guest',
            'vhost' => 'first',
            'exchange' => 'first',
        ]
    ]
];
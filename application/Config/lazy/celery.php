<?php
return [
    'celery' => [
        'default' => [
            'class' => \App\Utils\Pool\CeleryMqPool::class,
            'interval_check_time' => 120 * 1000, //检测mq链接的时间（s）
            'max_idle_time' => 30,  //最大空闲时间（s）, 至少大于10s
            'max_object_num' => 50,
            'min_object_num' => 3,
            'get_object_timeout' => 0.2,
            'host' => 'develop',
            'port' => 56729,
            'uname' => 'guest',
            'pwd' => 'guest',
            'vhost' => 'first',
            'exchange' => 'first',
            'connection_timeout' => 3,
            'read_write_timeout' => 2,
            'keepalive' => true,  //开启keepalive
            'heartbeat' => 0      //read_write_timeout must be at least 2x the heartbeat
        ]
    ]
];
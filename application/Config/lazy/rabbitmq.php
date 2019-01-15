<?php
return [
    'rabbitmq'=>[
        'default' => [
            'class' => \App\Utils\RabbitmqPool::class,
            'pool_size' => 15,
            'pool_get_timeout' => 0.5,
            'host' => 'develop',
            'port' => 56729,
            'uname' => 'guest',
            'pwd' => 'guest',
            'vhost' => 'first',
            'exchange' => 'first',
        ]
    ]
];
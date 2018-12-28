<?php
return [
    'env' => 'testing',
    'host' => '0.0.0.0',
    'port' => 9501,
    'worker_num' => 1,
    'mysql' => [
        'pool_size' => 3,     //连接池大小
        'pool_get_timeout' => 0.5, //当在此时间内未获得到一个连接，会立即返回。（表示所以的连接都已在使用中）
        'master' => [
            'host' => '127.0.0.1',   //数据库ip
            'port' => 3306,          //数据库端口
            'user' => 'root',        //数据库用户名
            'password' => 'brave', //数据库密码
            'database' => 'test',   //默认数据库名
            'timeout' => 0.5,       //数据库连接超时时间
            'charset' => 'utf8mb4', //默认字符集
            'strict_type' => true,  //ture，会自动表数字转为int类型
        ],
        'slave' => [
            [
                'host' => '127.0.0.1',   //数据库ip
                'port' => 3306,          //数据库端口
                'user' => 'root',        //数据库用户名
                'password' => 'brave', //数据库密码
                'database' => 'test',   //默认数据库名
                'timeout' => 0.5,       //数据库连接超时时间
                'charset' => 'utf8mb4', //默认字符集
                'strict_type' => true,  //ture，会自动表数字转为int类型
            ],
            [
                'host' => '127.0.0.1',   //数据库ip
                'port' => 3306,          //数据库端口
                'user' => 'root',        //数据库用户名
                'password' => 'brave', //数据库密码
                'database' => 'test',   //默认数据库名
                'timeout' => 0.5,       //数据库连接超时时间
                'charset' => 'utf8mb4', //默认字符集
                'strict_type' => true,  //ture，会自动表数字转为int类型
            ]
        ],
    ],
    'redis' => [
        'default'=>[
            'host'=>'develop',
            'port'=>6379,
            'options'=>[
                'connect_timeout'=>1,
                'timeout'=>1,
                'reconnect'=>5
            ],
            'pool_size'=>2,
            'pool_get_timeout'=>0.5
        ],
        'db'=>[
            'host'=>'127.0.0.1',
            'port'=>6379,
            'options'=>[
                'connect_timeout'=>1,
                'timeout'=>1,
                'reconnect'=>5
            ],
            'pool_size'=>2,
            'pool_get_timeout'=>0.5
        ],
    ],

    'router' => function (\FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/users', ['App\\Controller\\UserController', 'list']);
        $r->addRoute('GET', '/user/{uid:\d+}', 'App\\Controller\\UserController@user');
        $r->get('/add', ['App\\Controller\\UserController', 'add']);
        $r->get('/redis/get', ['App\\Controller\\RedisController', 'get']);
        $r->get('/redis/set', ['App\\Controller\\RedisController', 'set']);
        $r->get('/test', function () {
            return "i am test";
        });
        $r->post('/post', function () {
            return "must post method";
        });
    }
];
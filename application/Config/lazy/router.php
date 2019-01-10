<?php
return [
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
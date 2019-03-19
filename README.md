# dtsf
基于swoole4协程的框架


### 安装
```php
git clone xxxxx
cd xxxxx
composer install
```


###启动
```php
bin/dtsf.sh start  开启
bin/dtsf.sh stop   停止
bin/dtsf.sh restart   重启
bin/dtsf.sh reload   重启worker
```

###代码测试
Redis协程测试
```php
$result = '';
$redis = RedisDefaultDao::getInstance();
$redis->setex('key1', 300, 'test-test-1');
$redis->setex('ket2', 300, 'test-test-2');

$result .= "redis get key1<br>" . PHP_EOL;
\Dtsf\Coroutine\Coroutine::create(function () use ($redis, &$result){
    $val1 = $redis->get('key1');
    $result .= "redis key1 value: {$val1}<br>" . PHP_EOL;
});

$result .= "redis get key2<br>" . PHP_EOL;
\Dtsf\Coroutine\Coroutine::create(function () use ($redis, &$result){
    $val2 = $redis->get('key1');
    $result .= "redis key2 value: {$val2}<br>" . PHP_EOL;
});

Coroutine::sleep(1);

return "redis->end output <br> {$result}" . PHP_EOL;
```

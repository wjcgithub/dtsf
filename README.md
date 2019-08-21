# dtsf
基于swoole4协程开发的高性能框架

# 特点
1. 上下文支持
2. 已封装redis, mysql, celery-mq连接池
3. 支持apollo配置
4. docker的支持

## 环境配置

### 1.下载代码，初始化依赖
```php
git clone https://github.com/wjcgithub/dtsf.git
cd dtsf
composer install
```

### 2.启动docker（`打包运行环境，无需本地在配置`）
```php
docker pull wangjichao/dtsf_api
cd dtsf/docker
docker-compose up  or  docker-compose up -d
```
> 如果不需要apollo配置中心支持可先注释docker-compose中的`apollo_config`配置

### 3.测试
浏览器访问 `http://localhost:9505/index`

## 代码演示
### Redis协程测试, 测试代码如下
```php
$chan = new \chan(2);
$result = '';
$redis = RedisDefaultDao::getInstance();
$redis->setex('key1', 300, 'test-test-1');
$redis->setex('ket2', 300, 'test-test-2');

$result .= "redis get key1<br>" . PHP_EOL;
\Dtsf\Coroutine\Coroutine::create(function () use ($redis, $chan){
    $val1 = $redis->get('key1');
    $s1 = "redis key1 value: {$val1}<br>" . PHP_EOL;
    $chan->push($s1);

});

$result .= "redis get key2<br>" . PHP_EOL;
\Dtsf\Coroutine\Coroutine::create(function () use ($redis, $chan){
    $val2 = $redis->get('key1');
    $s2 = "redis key2 value: {$val2}<br>" . PHP_EOL;
    $chan->push($s2);
});

for ($i=0; $i<2; $i++) {
    $result.=$chan->pop();
}

return "redis->end output <br> {$result}" . PHP_EOL;
```

上面代码已经在控制器中存在,直接访问 http://localhost:9501/redis/test即可看到如下输出
![redis](redis.png)

### 性能测试
` ab -n1000 -c100 localhost:9501/redis/test`
![redis](bench.png)

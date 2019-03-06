<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-2-18
 * Time: 上午10:52
 */
while (1) {
    shell_exec('ab -n2000 -c500 -p /home/post.txt  -T application/x-www-form-urlencoded localhost:9501/msg');
    sleep(2);
}
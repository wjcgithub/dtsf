#!/bin/bash

while [ 1 ]
do
	ab -n 2000 -c150 -p /Users/yxp/post.txt -T application/x-www-form-urlencoded 127.0.0.1:9501/msg
	sleep 1
done

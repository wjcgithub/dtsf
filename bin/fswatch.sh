#!/bin/bash
DIR=$(cd `dirname $0`; pwd)
checkExt=php
checkTplExt=twig
echo `dirname $DIR`"1---------"
fswatch `dirname $DIR` | while read file
do
    echo $file"2---------"
    filename=$(basename "$file")
    extension="${filename##*.}"
    echo $extension"3---------"
    #php文件改动，则reload
    if [ "$extension" == "$checkExt" ];then
        #reload代码
        $DIR/family.sh reload
    fi

    #模板文件改动，则reload
    if [ "$extension" == "$checkTplExt" ];then
        #reload代码
        $DIR/family.sh reload
    fi
done
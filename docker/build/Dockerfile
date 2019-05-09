FROM php:alpine3.8
RUN echo http://mirrors.ustc.edu.cn/alpine/v3.8/main > /etc/apk/repositories && \
echo http://mirrors.ustc.edu.cn/alpine/v3.8/community >> /etc/apk/repositories && \
apk update && apk upgrade && \
apk add m4 autoconf make gcc g++ linux-headers
RUN docker-php-source extract && \
cd /usr/src/php/ext/sockets && \
phpize && \
./configure --enable-sockets && \
make && \
make install && \
make clean && \
docker-php-ext-enable sockets && \
docker-php-source delete
#ADD ./install/swoole-4.3.3.tgz /tmp/
RUN pecl install swoole-4.3.3 && \
pecl install seaslog && \
pecl install inotify && \
docker-php-ext-enable swoole && \
docker-php-ext-enable seaslog && \
docker-php-ext-enable inotify && \
apk update && apk add rabbitmq-c rabbitmq-c-dev && \
pecl install amqp && \
pecl install redis && \
docker-php-ext-enable amqp && \
docker-php-ext-enable redis
COPY seaslog_conf.ini /usr/local/etc/php/conf.d/
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
php composer-setup.php --install-dir=bin --filename=composer && \
php -r "unlink('composer-setup.php');"
WORKDIR /home/www-data/dtsf
#ENTRYPOINT ["sh"]
CMD ["php", "application/index.php"]

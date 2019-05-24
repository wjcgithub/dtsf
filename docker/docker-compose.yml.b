version: "2"
services:

  dtsf_server:
    image: swoole:v10
    container_name: dtsf_server
    restart: always
    mem_limit: 512m
    working_dir: /home/www-data/dtsf
#    networks:
#      net1:
#        ipv4_address: 10.9.0.10
    hostname: dtsf_server
    ports:
      - "9505:9501"
    volumes:
      - "/home/wwwroot/demo/php_file/swoole/dtsf:/home/www-data/dtsf"
    env_file:
#      - ./parameters.env
    environment:
#      - CONTAINER_NAME=rmqha_node0
#      - RABBITMQ_HOSTNAME=rmqha_node0
#      - RABBITMQ_NODENAME=rabbit

  apollo_config:
    image: swoole:v10
    container_name: apollo_config
    restart: always
    working_dir: /home/www-data/dtsf
    depends_on:
      - master
    mem_limit: 256m
#    networks:
#      net1:
#        ipv4_address: 10.9.0.11
    hostname: apollo_config
    volumes:
      - dtsf_server
    entrypoint: "/usr/local/bin/cluster_entrypoint.sh"
    command: "sh apollo/apollo_client"
    env_file:
#      - ./parameters.env
    environment:
#      - CONTAINER_NAME=rmqha_node1
#      - RABBITMQ_HOSTNAME=rmqha_node1
#      - RABBITMQ_NODENAME=rabbit
#      - RMQHA_RAM_NODE=true

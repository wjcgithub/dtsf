<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-4-11
 * Time: 下午17:28
 */

namespace App\Exceptions;


class ExceptionLog
{
    //server
    const SERVER_START = 'server_start';
    const SERVER_STOP = 'server_stop';
    const SERVER_EXIT = 'server_exit';
    const SERVER_SHUTDOWN = 'server_shutdown';
    const SERVER_ERROR = 'server_error';
    const SERVER_REQUEST = 'server_request';
    const MEMORY_USE = 'memory_use';
    const CORO_INFO = 'coroutine_info';
    
    const DEBUG = 'debug_info';
    
    //rabbitmq
    const MQ_ACK_ERROR = 'mq_ack_error';
    const MQ_CONFIRM_ERROR = 'mq_confirm_error';
    
    const ERROR_HANDLER = 'error_handler_log';
    const SHUTDOWN_ERROR = 'shutdown_error_log';
    
    const DTQ_PRODUCER_ERROR = 'dtq_producer_error';
    const DTQ_PRODUCER_TO_MQ_ERROR = 'dtq_producer_to_rabbitmq_error';
    const DTQ_ORIGIN_MSG = 'dtq_origin_msg';
    const GET_OBJECT_POOL='get_object_from_pool';
    
    const DB_ERROR = 'db_error_log';
    
    //pool
    const POOL_NUM = 'pool_num';
    const POOL_REDIS_LOG = 'pool_redis_log';
}
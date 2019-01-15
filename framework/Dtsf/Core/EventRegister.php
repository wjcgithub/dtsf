<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-15
 * Time: 上午11:55
 */

namespace Dtsf\Core;


class EventRegister extends MultiContainer
{
    const onStart = 'start';
    const onShutdown = 'shutdown';
    const onWorkerStart = 'workerStart';
    const onWorkerStop = 'workerStop';
    const onWorkerExit = 'workerExit';
    const onTimer = 'timer';
    const onConnect = 'connect';
    const onReceive = 'receive';
    const onPacket = 'packet';
    const onClose = 'close';
    const onBufferFull = 'bufferFull';
    const onBufferEmpty = 'bufferEmpty';
    const onTask = 'task';
    const onFinish = 'finish';
    const onPipeMessage = 'pipeMessage';
    const onWorkerError = 'workerError';
    const onManagerStart = 'managerStart';
    const onManagerStop = 'managerStop';
    const onRequest = 'request';
    const onHandShake = 'handShake';
    const onMessage = 'message';
    const onOpen = 'open';

    function __construct(array $allowKeys = null)
    {
        parent::__construct([
            'start','shutdown','workerStart','workerStop','workerExit','timer',
            'connect','receive','packet','close','bufferFull','bufferEmpty','task',
            'finish','pipeMessage','workerError','managerStart','managerStop',
            'request','handShake','message','open'
        ]);
    }
}
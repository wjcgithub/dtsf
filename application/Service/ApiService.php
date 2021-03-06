<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-16
 * Time: 下午5:34
 */

namespace App\Service;

use App\Dao\CeleryMqDao;
use App\Dao\MsgDao;
use App\Dao\ProducerErrorMsgDao;
use App\Dao\QueueDao;
use App\Dao\RabbitMqDao;
use App\Dao\RedisDefaultDao;
use App\Dao\TasksDao;
use App\Entity\Result;
use App\Exceptions\ExceptionLog;
use App\Exceptions\GetDaoException;
use App\Exceptions\GetTaskInfoException;
use App\Exceptions\InsertMsgToDbException;
use Dtsf\Core\Log;
use Dtsf\Core\WorkerApp;
use Dtsf\Db\Redis\DtRedisReException;

/**
 * Class ApiService
 * @package App\Service
 */
class ApiService extends AbstractService
{
    const TASKPREX = 'celery:task';
    const CACHETIMEOUT = 3600 * 24;  //单位秒
    const REDIS_TIMEOUT = 2;
    
    //Tasks
    const TASK_STATUS_ENABLE = 1;
    const TASK_STATUS_DISABLE = 0;
    const TASK_STATUS_DELETE = 2;
    
    //msg
    const ACK = 1;
    const UNACK = 0;
    
    /**
     * @param string $msgid
     * @param $tid
     * @param $payload
     * @return mixed
     */
    public function PostTask($msgid = '', $tid, $payload, $op)
    {
        $qResult = Result::getCoInstance();
        try {
            $qResult = $this->getTaskInfoByTid($tid);
            if ($qResult->getCode() == Result::CODE_ERROR) {
                return $qResult->toJson();
            }
            
            $taskInfo = $qResult->getData();
            $paramsArr = [];
            //task message body
            $paramsArr['p'] = $payload;
            //task callback or command
            $paramsArr['c'] = $taskInfo['callback_url'];
            //task type
            $paramsArr['t'] = $taskInfo['type'];
            $paramsArr['tid'] = $taskInfo['tid'];
            if (!$op['fast']) {
                if (empty($msgid)) {
                    $msgid = uniqid($taskInfo['taskName'], TRUE);
                    $msgRes = MsgDao::getInstance()->add([
                        'msgid' => $msgid,
                        'tid' => $tid,
                        'payload' => $payload,
                        'status' => self::UNACK,
                        'count' => 0,
                        'ctime' => date('Y-m-d H:i:s')
                    ]);
                    if (!$msgRes) {
                        throw new InsertMsgToDbException('insert msg to db error');
                    }
                }
            } else {
                $msgid = uniqid($taskInfo['taskName'], TRUE);
            }
            
            \Dtsf\Coroutine\Coroutine::create(function () use ($msgid, $tid, $payload, $qResult, $taskInfo, $paramsArr) {
                try {
                    $payloadArr = json_decode($payload, 1);
                    $payloadArr['msgid'] = $msgid;
                    $paramsArr['p'] = json_encode($payloadArr);
//                    var_dump(base64_encode($paramsArr['p']));
                    
                    CeleryMqDao::getInstance()->insert(
                        $msgid,
                        $taskInfo['taskName'],
                        ['payload' => json_encode($paramsArr)],
                        $taskInfo['queueName']
                    );
                } catch (\InvalidArgumentException $e) {
                    $msg = '普通异常-----code: ' . $e->getCode() . 'msg: ' . $e->getMessage() . 'trace: ' . $e->getTraceAsString();
                    Log::error($msg, [], ExceptionLog::DTQ_PRODUCER_ERROR);
                } catch (\Throwable $throwable) {
                    Log::error("{worker_id} post to mq error on coroutine, and current app status is {status}, msg: {msg}."
                        , [
                            '{worker_id}' => posix_getppid(),
                            '{status}' => WorkerApp::getInstance()->serverStatus,
                            '{msg}' => $throwable->getMessage() . '====trace:' . $throwable->getTraceAsString()
                        ]
                        , ExceptionLog::DTQ_PRODUCER_ERROR);
                }
            });
            $qResult->setCode(Result::CODE_SUCCESS)->setData($msgid)->setMsg('success');
        } catch (\InvalidArgumentException $e) {
            $msg = '普通异常-----code: ' . $e->getCode() . 'msg: ' . $e->getMessage() . 'trace: ' . $e->getTraceAsString();
            Log::error($msg, [], ExceptionLog::DTQ_PRODUCER_ERROR);
            $qResult->setCode(Result::CODE_ERROR)->setMsg($e->getMessage())->setData([]);
        } catch (GetDaoException $e) {
            $this->performExcepiton($e, $msgid, $tid, $payload, $qResult);
            $qResult->setCode(Result::CODE_ERROR)->setMsg('获取链接失败')->setData([]);
        } catch (GetTaskInfoException $e) {
            $this->performExcepiton($e, $msgid, $tid, $payload, $qResult);
            $qResult->setCode(Result::CODE_ERROR)->setMsg('获取任务信息失败')->setData([]);
        } catch (InsertMsgToDbException $e) {
            $this->performExcepiton($e, $msgid, $tid, $payload, $qResult);
            $qResult->setCode(Result::CODE_ERROR)->setMsg("保存消息到db失败")->setData([]);
        } catch (\Throwable $throwable) {
            $this->performExcepiton($throwable, $msgid, $tid, $payload, $qResult);
            Log::error("{worker_id} 致命错误, and current app status is {status}, msg: {msg}."
                , [
                    '{worker_id}' => posix_getppid(),
                    '{status}' => WorkerApp::getInstance()->serverStatus,
                    '{msg}' => $throwable->getMessage() . '====trace:' . $throwable->getTraceAsString()
                ]
                , ExceptionLog::DTQ_PRODUCER_ERROR);
            $qResult->setCode(Result::CODE_ERROR)->setMsg('操作失败')->setData([]);
        }
        
        return $qResult->toJson();
    }
    
    /**
     * @param $tid
     * @return mixed
     */
    private function getTaskInfoByTid($tid)
    {
        $result = Result::getInstance();
        $redis = null;
        try {
            $taskInfoStr = '';
            $redis = RedisDefaultDao::getInstance();
            //这里单独链接，是为了设置超时时间，而不影获取任务信息异常响其他使用者
            $mtid = $this->makeTid($tid);
            //缓存不存在，回写缓存
            if (empty($taskInfoStr = $redis->get($mtid))) {
                $taskInfo = $this->generateCacheArr($tid);
                if (empty($taskInfo)) {
                    throw new GetTaskInfoException('从数据库获取任务信息失败_1');
                }
                $taskInfoStr = json_encode($taskInfo);
                $redis->setex($mtid, self::CACHETIMEOUT, $taskInfoStr);
            }
            if (!empty($taskInfoStr)) {
                $result->setCode(Result::CODE_SUCCESS)->setMsg('success')->setData(json_decode($taskInfoStr, True));
            } else {
                $result->setCode(Result::CODE_ERROR)->setMsg('获取任务信息失败');
            }
        } catch (DtRedisReException $e) {
            //缓存链接失败，或超时，读取数据库，并返回结果，防止缓存关掉接口不能用
            $taskInfo = $this->generateCacheArr($tid);
            if (empty($taskInfo)) {
                throw new GetTaskInfoException('从数据库获取任务信息失败_2');
            }
            $result->setCode(Result::CODE_SUCCESS)->setMsg('success')->setData($taskInfo);
            Log::error('redis服务链接异常, host:', [], ExceptionLog::DTQ_PRODUCER_ERROR);
        } catch (\InvalidArgumentException $e) {
            Log::error($e->getMessage() . '-----' . $e->getTraceAsString(), [], ExceptionLog::DTQ_PRODUCER_ERROR);
            $result->setCode(Result::CODE_ERROR)->setMsg($e->getMessage());
        } catch (\Exception $e) {
            $result->setCode(Result::CODE_ERROR)->setMsg('获取任务信息异常');
            Log::error($e->getMessage() . '-----' . $e->getTraceAsString(), [], ExceptionLog::DTQ_PRODUCER_ERROR);
        }
        return $result;
    }
    
    /**
     * 生成要存储的缓存结构
     *
     * @param $tid
     * @return array
     */
    private function generateCacheArr($tid)
    {
        $cacheValueArr = [];
        try {
            $taskInfo = $this->fetchTaskInfo($tid);
            if (!empty($taskInfo) && !empty($taskInfo->queueid)) {
                $where = "id = '{$taskInfo->queueid}'";
                $fields = 'name';
                $queueInfo = QueueDao::getInstance()->fetchEntity($where, $fields);
                if (!empty($queueInfo) && !empty($queueInfo->name)) {
                    $cacheValueArr['queueName'] = 'group' . $taskInfo->groupid . '_' . $queueInfo->name;
                    $cacheValueArr['taskName'] = $queueInfo->name . '.task.handler';
                    $cacheValueArr['callback_url'] = $taskInfo->callback_url;
                    $cacheValueArr['type'] = $taskInfo->type;
                    $cacheValueArr['tid'] = $taskInfo->id;
                } else {
                    throw new \InvalidArgumentException('该任务所在队列不存在');
                }
            } else {
                throw new \InvalidArgumentException('该任务不存在, 或者已被禁用');
            }
        } catch (\Exception $e) {
            Log::error('生成缓存失败--msg:' . $e->getMessage() . '---trace:' . $e->getTraceAsString(), [], ExceptionLog::DTQ_PRODUCER_ERROR);
        }
        return $cacheValueArr;
    }
    
    
    /**
     * 查询队列信息
     *
     * @param $tid
     * @return mixed
     */
    private function fetchTaskInfo($tid)
    {
        $where = "tid = '{$tid}' and status = " . self::TASK_STATUS_ENABLE;
        $fields = '*';
        return TasksDao::getInstance()->fetchEntity($where, $fields);
    }
    
    /**
     * 组装mtid
     *
     * @param $tid
     * @return string
     */
    private function makeTid($tid)
    {
        return self::TASKPREX . ':' . $tid;
    }
    
    /**
     * 处理redis, rabbitmq异常的情况
     *
     * @param $e
     * @param $tid
     * @param $payload
     * @param $result
     * @return mixed
     */
    private function performExcepiton($e, $msgid, $tid, $payload, $result)
    {
        $this->logMsgInfoOnException($tid, $payload);
        $msg = '异常-----code: ' . $e->getCode() . 'msg: ' . $e->getMessage() . 'trace: ' . $e->getTraceAsString();
        Log::error($msg, [], ExceptionLog::DTQ_PRODUCER_ERROR);
        $errorParam = [];
        try {
            $errorParam['msgid'] = $msgid;
            $errorParam['tid'] = $tid;
            $errorParam['payload'] = $payload;
            $errorParam['msg'] = $e->getMessage();
            $errorParam['ctime'] = date('Y-m-d H:i:s');
            ProducerErrorMsgDao::getCoInstance()->add($errorParam);
        } catch (\Exception $e) {
            Log::error('保存投递失败消息失败----msg' . $e->getMessage() . "--body:" . json_encode($errorParam), [], ExceptionLog::DTQ_PRODUCER_TO_MQ_ERROR);
        }
        
        $result->setCode(Result::CODE_SUCCESS)->setMsg('success');
        return $result;
    }
    
    /**
     * 当异常发生时，记录原始数据
     *
     * @param $tid
     * @param $payload
     */
    private function logMsgInfoOnException($tid, $payload)
    {
        Log::error('tid:' . $tid . '----payload' . $payload, [], ExceptionLog::DTQ_ORIGIN_MSG);
    }
}
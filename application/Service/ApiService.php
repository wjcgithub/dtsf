<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-16
 * Time: 下午5:34
 */

namespace App\Service;

use App\Dao\CeleryMqDao;
use App\Dao\QueueDao;
use App\Dao\RabbitMqDao;
use App\Dao\RedisDefaultDao;
use App\Dao\TasksDao;
use App\Entity\Result;
use Dtsf\Core\Log;
use Dtsf\Db\Redis\DtRedisReException;

class ApiService extends AbstractService
{
    const TASKPREX = 'celery:task';
    const CACHETIMEOUT = 3600 * 24;  //单位秒
    const REDIS_TIMEOUT = 2;

    //Tasks
    const TASK_STATUS_ENABLE = 1;
    const TASK_STATUS_DISABLE = 0;
    const TASK_STATUS_DELETE = 2;

    /**
     * @param string $msgid
     * @param $tid
     * @param $payload
     * @return mixed
     */
    public function PostTask($msgid = '', $tid, $payload)
    {
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
        if(empty($msgid)) {
            $msgid = uniqid($taskInfo['taskName'], TRUE);
        }

        $cresult = CeleryMqDao::getInstance()->insert(
            $msgid,
            $taskInfo['taskName'],
            ['payload' => json_encode($paramsArr)],
            $taskInfo['queueName']
        );
        $qResult->setCode(Result::CODE_SUCCESS)->setData($cresult->getId())->setMsg('success');
        return $qResult->toJson();
    }

    /**
     * @param $tid
     * @return mixed
     */
    private function getTaskInfoByTid($tid)
    {
        $result = Result::getRequestInstance();
        $redis = null;
        try {
            $taskInfoStr = '';
            $redis = RedisDefaultDao::getRequestInstance();
            //这里单独链接，是为了设置超时时间，而不影响其他使用者
            $mtid = $this->makeTid($tid);
            //缓存不存在，回写缓存
            if (empty($taskInfoStr = $redis->get($mtid))) {
                $taskInfo = $this->generateCacheArr($tid);
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
            $result->setCode(Result::CODE_SUCCESS)->setMsg('success')->setData($taskInfo);
            Log::error('redis服务链接异常, host:');
        } catch (\InvalidArgumentException $e) {
            Log::error($e->getMessage().'-----'.$e->getTraceAsString());
            $result->setCode(Result::CODE_ERROR)->setMsg($e->getMessage());
        } catch (\Exception $e) {
            $result->setCode(Result::CODE_ERROR)->setMsg('获取任务信息异常');
            Log::error($e->getMessage().'-----'.$e->getTraceAsString());
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
        $taskInfo = $this->fetchTaskInfo($tid);
        $where = "id = '{$taskInfo->queueid}'";
        $fields = 'name';
        $queueInfo = QueueDao::getRequestInstance()->fetchEntity($where, $fields);
        if (!empty($taskInfo)) {
            $cacheValueArr['queueName'] = 'group' . $taskInfo->groupid . '_' . $queueInfo->name;
            $cacheValueArr['taskName'] = $queueInfo->name . '.task.handler';
            $cacheValueArr['callback_url'] = $taskInfo->callback_url;
            $cacheValueArr['type'] = $taskInfo->type;
            $cacheValueArr['tid'] = $taskInfo->id;
        } else {
            throw new \InvalidArgumentException('该任务不存在, 或者已被禁用');
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
        return TasksDao::getRequestInstance()->fetchEntity($where, $fields);
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
}
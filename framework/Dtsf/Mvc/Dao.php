<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:04
 */

namespace Dtsf\Mvc;


use Dtsf\Core\Config;
use Dtsf\Core\Log;
use Dtsf\Coroutine\Coroutine;
use EasySwoole\Component\Pool\PoolManager;

class Dao
{
    /**
     * @var string mysql 链接
     */
    protected $connection = '';

    /**
     * @var entity名
     */
    protected $entity;

    /**
     * @var mysql连接数组
     * @desc 不同协程不能复用mysql连接，所以通过协程id进行资源隔离
     */
    private $dbs;

    /**
     * @var Mysql
     */
    protected $db;

    //表名
    protected $table;

    //主键字段名
    protected $pkId;
    /**
     * @var 数据库配置名称, 用于处理多个数据库
     */
    private $dbTag;


    public function __construct($entity)
    {
        $this->entity = $entity;
        $entityRef = new \ReflectionClass($this->entity);
        $this->connection = $entityRef->getConstant('CONNECTION');
        $this->table = $entityRef->getConstant('TABLE_NAME');
        $this->pkId = $entityRef->getConstant('PK_ID');
    }

    /**
     * @param $dbTag
     * @desc 更换数据库连接池
     */
    public function setDbName($dbTag)
    {
        $this->dbTag = $dbTag;
    }

    /**
     * 获取一个链接类型的db实例
     *
     * @param string $dbTag
     * @return mixed
     */
    public function getDb()
    {
        $coId = Coroutine::getId();
        if (empty($this->dbs[$coId])) {
            //不同协程不能复用mysql链接, 所以通过协程id进行资源隔离
            //达到同一个协程只用一个mysql链接, 不同协程用不同的mysql链接
            $this->dbs[$coId] = PoolManager::getInstance()->getPool(Config::get('mysql.' . $this->connection . '.class'))
                ->getObj(Config::get('mysql.' . $this->connection . '.pool_get_timeout'));
            defer(function () {
                $this->recycle();
            });
        }

        if (empty($this->dbs[$coId])) {
            Log::emergency($this->connection . '数据库不够用了', [], 'dbpool');
        }
        return $this->dbs[$coId];

    }

    /**
     * @throws \Exception
     * @desc mysql资源回收到连接池
     */
    public function recycle()
    {
        print_r("current pool count: ".count($this->dbs). "\r\n");
        $coId = Coroutine::getId();
        if (!empty($this->dbs[$coId])) {
            $mysqlPool = $this->dbs[$coId];
            unset($this->dbs[$coId]);
            echo "Dao Coroutine".\Swoole\Coroutine::getuid()."-- defer event\r\n";
            $t = PoolManager::getInstance()->getPool(Config::get('mysql.' . $this->connection . '.class'));
            print_r('release before:'.$t->getLength()."\r\n");
            $t->recycleObj($mysqlPool);
            print_r('release after:'.$t->getLength()."\r\n");
        }
    }

    /**
     * @return mixed
     * @desc 获取表名
     */
    public function getLibName()
    {
        return $this->table;
    }

    /**
     * @param $id
     * @param string $fields
     * @return mixed
     * @desc 通过主键查询记录
     */
    public function fetchById($id, $fields = '*')
    {
//        $start = microtime(true);
//        $this->db->query("select sleep(5)");
//        echo "我是第一个sleep五秒之后\n";
//        $ret = $this->db->query("select id from student limit 1");#2
//        var_dump($ret);
//        $use = microtime(true) - $start;
//        echo "协程mysql输出用时：" . $use . PHP_EOL;
        return $this->fetchEntity("{$this->pkId} = {$id}", $fields);
    }

    /**
     * @param string $where
     * @param string $fields
     * @param null $orderBy
     * @return mixed
     * @desc 通过条件查询一条记录，并返回一个entity
     */
    public function fetchEntity($where = '1', $fields = '*', $orderBy = null)
    {
        $result = $this->fetchArray($where, $fields, $orderBy, 1);
        if (!empty($result[0])) {
            return new $this->entity($result[0]);
        }
        return null;
    }

    /**
     * @param string $where
     * @param string $fields
     * @param null $orderBy
     * @param int $limit
     * @return mixed
     * @desc 通过条件查询记录列表，并返回entity列表
     */
    public function fetchAll($where = '1', $fields = '*', $orderBy = null, $limit = 0)
    {
        $result = $this->fetchArray($where, $fields, $orderBy, $limit);
        if (empty($result)) {
            return $result;
        }
        foreach ($result as $index => $value) {
            $result[$index] = new $this->entity($value);
        }
        return $result;
    }


    /**
     * @param string $where
     * @param string $fields
     * @param null $orderBy
     * @param int $limit
     * @return mixed
     * @desc 通过条件查询
     */
    public function fetchArray($where = '1', $fields = '*', $orderBy = null, $limit = 0)
    {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " order by {$orderBy}";
        }

        if ($limit) {
            $query .= " limit {$limit}";
        }

        echo "查询开始前.......\r\n";
//        \Swoole\Coroutine::sleep(2);
//        $res = $this->getDb()->rawQuery($query);
        $res = $this->getDb()->safeQuery($query);
        echo "查询中.......\r\n";
        echo "查询完毕.......\r\n";
        return $res;
    }

    /**
     * @param array $array
     * @return bool
     * @desc 插入一条记录
     */
    public function add(array $array)
    {
        $strFields = '`' . implode('`,`', array_keys($array)) . '`';
        $strValues = "'" . implode("','", array_values($array)) . "'";
        $query = "INSERT INTO {$this->getLibName()} ({$strFields}) VALUES ({$strValues})";
        if (!empty($onDuplicate)) {
            $query .= 'ON DUPLICATE KEY UPDATE ' . $onDuplicate;
        }
        $result = $this->db->query($query);
        if (!empty($result['insert_id'])) {
            return $result['insert_id'];
        }

        return false;
    }

    /**
     * @param array $array
     * @param $where
     * @return bool
     * @throws \Exception
     * @desc 按条件更新记录
     */
    public function update(array $array, $where)
    {
        if (empty($where)) {
            throw new \Exception('update 必需有where条件限定');
        }
        $strUpdateFields = '';
        foreach ($array as $key => $value) {
            $strUpdateFields .= "`{$key}` = '{$value}',";
        }
        $strUpdateFields = rtrim($strUpdateFields, ',');
        $query = "UPDATE {$this->getLibName()} SET {$strUpdateFields} WHERE {$where}";
        echo $query;
        $result = $this->db->query($query);
        return $result['affected_rows'];
    }

    /**
     * @param $where
     * @return mixed
     * @throws \Exception
     * @desc 按条件删除记录
     */
    public function delete($where)
    {
        if (empty($where)) {
            throw new \Exception('delete 必需有where条件限定');
        }

        $query = "DELETE FROM {$this->getLibName()} WHERE {$where}";
        $result = $this->db->query($query);
        return $result['affected_rows'];
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-18
 * Time: 下午20:50
 */

namespace App\Entity;
use Dtsf\Core\Singleton;


/**
 * Class Result
 * @package App\Entity
 */
class Result
{
    use Singleton;

    /**
     *
     * @var int -1:失败 1：成功 200：更新 -401:没有权限
     */
    const CODE_SUCCESS = 1;

    const CODE_ERROR = -1;

    const CODE_UPDATE = 2;

    const CODE_NOAUTH = -401;

    public $reslut_arr = [
        'code' => -1,
        'msg' => '',
        'data' => '',
        'count' => 0
    ];

    public function __construct()
    {
        $this->init(self::CODE_SUCCESS, 'success', []);
    }

    /**
     * 初始化
     * @param int $code
     * @param string $msg
     * @param string $data
     * @param int $cnt
     */
    private function init($code, $msg = '', $data = '', $cnt = 0)
    {
        $this->reslut_arr = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'count' => $cnt
        ];
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->reslut_arr['code'] = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->reslut_arr['code'];
    }

    /**
     * @param string $msg 原因
     */
    public function setMsg($msg)
    {
        $this->reslut_arr['msg'] = $msg;
        return $this;
    }

    public function getMsg()
    {
        return $this->reslut_arr['msg'];
    }

    /**
     * @param array $data object数据
     */
    public function setData($data)
    {
        $this->reslut_arr['data'] = $data;
        return $this;
    }

    public function getData()
    {
        return $this->reslut_arr['data'];
    }

    /**
     * @param int $cnt data数组个数
     */
    public function setCnt($cnt)
    {
        $this->reslut_arr['count'] = $cnt;
        return $this;
    }

    public function getCnt()
    {
        return $this->reslut_arr['count'];
    }

    /**
     * 填充结果
     *
     * @param $e
     */
    public function fillResult($e)
    {
        $this->setCode($e->getCode());
        $this->setMsg($e->getMessage());
    }

    /**
     * @return array 输出数组
     */
    public function toArray()
    {
        return $this->reslut_arr;
    }

    /**
     * 规范化的json
     *
     * @param int $code （-1:失败 1：成功 2：更新 -401:没有权限）
     * @param string $msg
     * @param string $data
     * @param int $cnt （data数组个数）
     */
    public function toJson($option = 0)
    {
        return json_encode($this->reslut_arr, $option);
    }
}

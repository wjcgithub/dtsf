<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午2:46
 */

namespace Dtsf\Coroutine;

use EasySwoole\Http\Request as EsRequest;
use EasySwoole\Http\Response as EsResponse;
use Swoole\Http\Response;

class Context
{
    /**
     * @var swoole_http_request
     */
    private $request;

    /**
     * @var swoole_http_response
     */
    private $response;

    /**
     * @var array 一个array, 来存取想要的数据
     */
    private $map = [];

    public function __construct(EsRequest $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return swoole_http_request|\Swoole\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return swoole_http_response|\Swoole\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $key
     * @param $val
     */
    public function set($key, $val)
    {
        $this->map[$key] = $val;
    }

    /**
     * @return array
     */
    public function get($key)
    {
        if (isset($this->map[$key])) {
            return $this->map[$key];
        }

        return null;
    }
}
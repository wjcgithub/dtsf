<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午6:39
 */

namespace Dtsf\Mvc;


use Dtsf\Pool\ContextPool;

class Controller
{
    protected $request;

    const CODE_SUCCESS = 200;
    const _CONTROLLER_KEY_ = '__CTR__';
    const _METHOD_KEY_ = '__METHOD__';

    public function __construct()
    {
        $context = ContextPool::get();
        $this->request = $context->getRequest();
    }
}
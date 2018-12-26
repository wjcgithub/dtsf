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

    public function __construct()
    {
        $context = ContextPool::getContext();
        $this->request = $context->getRequest();
    }
}
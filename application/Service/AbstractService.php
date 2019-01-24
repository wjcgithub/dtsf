<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-16
 * Time: 下午6:08
 */

namespace App\Service;


use Dtsf\Core\Singleton;
use Dtsf\Pool\ContextPool;

abstract class AbstractService
{
    use Singleton;

    protected $serv;
    protected $context;

    function __construct()
    {
        $this->context = ContextPool::get();
//        $this->serv = $this->context->get('serv');
    }
}
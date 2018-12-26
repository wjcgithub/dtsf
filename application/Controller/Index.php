<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:30
 */

namespace App\Controller;


use Dtsf\Pool\Context;

class Index
{
    public function index()
    {
        //通过context拿到$request
        $context = Context::getContext();
        $request = $context->getRequest();
        return 'i am family by route'. json_encode($request->get);
    }

    public function test()
    {
        return 'i am test';
    }
}
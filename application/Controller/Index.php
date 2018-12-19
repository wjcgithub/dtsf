<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-19
 * Time: 下午12:30
 */

namespace App\Controller;


class Index
{
    public function index()
    {
        return 'i am family by route';
    }

    public function test()
    {
        return 'i am test';
    }
}
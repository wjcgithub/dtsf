<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-16
 * Time: 下午5:34
 */

namespace App\Service;


use Dtsf\Core\Singleton;
use Dtsf\Dtsf;

class ApiService
{
    use Singleton;

    public function PostTask()
    {
        $serv = Dtsf::$Di->get('serv');
        print_r($serv);
    }
}
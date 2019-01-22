<?php

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-18
 * Time: 下午2:19
 */
namespace App\Controller\Validates;

use Dtsf\Core\Singleton;

class ApiValidate extends \EasySwoole\Validate\Validate
{
    use Singleton;

    /**
     * @param $data
     * @return bool
     */
    public function PostTaskValidate($data)
    {
        $this->addColumn('messageno', 'messageno')->required('messageno is empty');
        $this->addColumn('messagebody', 'messagebody')->required('messagebody is empty');
        return $this->validate($data);
    }
}
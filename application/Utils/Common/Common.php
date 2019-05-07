<?php
namespace App\Utils\Common;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-5-7
 * Time: 下午3:51
 */
class Common
{
    /**
     * scan dir
     *
     * @param $dir
     * @return array
     */
    static public function scanDir($dir)
    {
        $data = array();
        if (is_dir($dir)) {
            //是目录的话，先增当前目录进去
            $data[] = $dir;
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                $data = array_merge($data, self::scanDir($dir . "/" . $file));
            }
        } else {
            $data[] = $dir;
        }
        return $data;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 18-12-26
 * Time: 下午5:51
 */

namespace Dtsf\Mvc;


class Entity
{
    /**
     * @desc 把结果数组填充到entiry
     *
     * Entity constructor.
     * @param array $array
     */
    public function __construct(array $array)
    {
        if (empty($array)) {
            return $this;
        }

        foreach ($array as $key=>$value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
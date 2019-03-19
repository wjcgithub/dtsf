<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/22
 * Time: 下午2:53
 */

namespace EasySwoole\Spl;


/*
 * 仅能获取protected 和public成员变量
 */

use EasySwoole\Spl\Exception\Exception;

class SplBean implements \JsonSerializable
{
    const FILTER_NOT_NULL = 1;
    const FILTER_NOT_EMPTY = 2;//0 不算empty

    private $_keyMap = [];
    private $_classMap = [];

    public function __construct(array $data = null,$autoCreateProperty = false)
    {
        $this->_keyMap = $this->setKeyMapping();
        $this->_classMap = $this->setClassMapping();
        if($data){
            $this->arrayToBean($data,$autoCreateProperty);
        }
        $this->initialize();
        $this->classMap();
    }

    final public function allProperty():array
    {
        $data = [];
        foreach ($this as $key => $item){
            array_push($data,$key);
        }
        unset($data['_keyMap']);
        unset($data['_classMap']);
        return $data;
    }

    function toArray(array $columns = null,$filter = null):array
    {
        $data = $this->jsonSerialize();
        if($columns){
            $data = array_intersect_key($data, array_flip($columns));
        }
        if($filter === self::FILTER_NOT_NULL){
            return array_filter($data,function ($val){
                return !is_null($val);
            });
        }else if($filter === self::FILTER_NOT_EMPTY){
            return array_filter($data,function ($val){
                if($val === 0 || $val === '0'){
                    return true;
                }else{
                    return !empty($val);
                }
            });
        }else if(is_callable($filter)){
            return array_filter($data,$filter);
        }
        return $data;
    }

    /*
     * 返回转化后的array
     */
    function toArrayWithMapping(array $columns = null,$filter = null)
    {
        $array = $this->toArray();
        if(!empty($this->_keyMap)){
            foreach ($this->_keyMap as $beanKey => $dataKey) {
                if(array_key_exists($beanKey,$array)){
                    $array[$dataKey] = $array[$beanKey];
                    unset($array[$beanKey]);
                }
            }
        }
        if($columns){
            $array = array_intersect_key($array, array_flip($columns));
        }
        if($filter === self::FILTER_NOT_NULL){
            return array_filter($array,function ($val){
                return !is_null($val);
            });
        }else if($filter === self::FILTER_NOT_EMPTY){
            return array_filter($array,function ($val){
                if($val === 0 || $val === '0'){
                    return true;
                }else{
                    return !empty($val);
                }
            });
        }else if(is_callable($filter)){
            return array_filter($array,$filter);
        }
        return $array;
    }

    final private function arrayToBean(array $data,$autoCreateProperty = false):SplBean
    {
        //先做keyMap转化
        if(!empty($this->_keyMap)){
            foreach ($this->_keyMap as $beanKey => $dataKey) {
                if(array_key_exists($dataKey,$data)){
                    $data[$beanKey] = $data[$dataKey];
                    unset($data[$dataKey]);
                }
            }
        }
        if($autoCreateProperty == false){
            $data = array_intersect_key($data,array_flip($this->allProperty()));
        }
        foreach ($data as $key => $item){
            $this->addProperty($key,$item);
        }
        return $this;
    }

    final public function addProperty($name,$value = null):void
    {
        $this->$name = $value;
    }

    final public function getProperty($name)
    {
        if(isset($this->$name)){
            return $this->$name;
        }else{
            return null;
        }
    }

    final public function jsonSerialize():array
    {
        $data = [];
        foreach ($this as $key => $item){
            $data[$key] = $item;
        }
        unset($data['_keyMap']);
        unset($data['_classMap']);
        return $data;
    }

    /*
     * 在子类中重写该方法，可以在类初始化的时候进行一些操作
     */
    protected function initialize():void
    {

    }

    /*
     * 如果需要用到keyMap  请在子类重构并返回对应的map数据
     * return ['dataKey'=>'beanKey']
     */
    protected function setKeyMapping():array
    {
        return [];
    }
    /*
     * return ['property'=>class string]
     */
    protected function setClassMapping():array
    {
        return [];
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize(),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    /*
     * 恢复到属性定义的默认值
     */
    public function restore(array $data = [])
    {
        $this->arrayToBean($data+get_class_vars(static::class));
        $this->initialize();
        $this->classMap();
        return $this;
    }

    private function classMap()
    {
        if(!empty($this->_classMap)){
            $propertyList = $this->allProperty();
            foreach ($this->_classMap as $property => $class){
                if(in_array($property,$propertyList)){
                    $val = $this->$property;
                    $force = true;
                    if(strpos($class,'@') !== false){
                        $force = false;
                        $class = substr($class,1);
                    }
                    if(is_object($val)){
                        if(!$val instanceof $class){
                            throw new Exception("value for property:{$property} dot not match in ".(static::class));
                        }
                    }else if($val === null){
                        if($force){
                            $this->$property = $this->createClass($class);
                        }
                    }else{
                        $this->$property = $this->createClass($class,$val);
                    }
                }else{
                    throw new Exception("property:{$property} not exist in ".(static::class));
                }
            }
        }
    }

    /**
     * @param string $class
     * @param null $arg
     * @return object
     * @throws \ReflectionException
     */
    private function createClass(string $class, $arg = null)
    {
        $ref = new \ReflectionClass($class);
        return $ref->newInstance($arg);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Mikkle
 * QQ:776329498
 * Date: 2017/11/20
 * Time: 16:59
 */

namespace app\base\service\base;

use app\base\service\center\OptionsCenter;
use mikkle\tp_redis\Redis;

abstract class InfoBase
{
    static protected $instance;
    protected $redis;
    protected $infoString ;
    protected $infoId;
    protected $error;
    public function __construct($info_id)
    {
        $this->redis = new Redis(OptionsCenter::$redisInfoCenter);
        $this->_initialize();
        if ($info_id){
            $this->infoId = $info_id ."_";
        }
    }

    public static function instance($info_id)
    {
        $sn = md5(json_encode($info_id));
        if (self::$instance[$sn]){
            return self::$instance[$sn];
        }
        return  new static($info_id);
    }

    abstract public function _initialize();


    public function addError($error){
        $this->error = is_string($error) ? $error : json_encode($error);
    }
    public function getError(){
        return $this->error;
    }

    public function setInfoArray($array){
        $key=$this->createInfoKey();
        return $this->redis->hMset($key,$array) ? true : false ;
    }

    public function setInfoFieldValue($field,$value){
        $key=$this->createInfoKey();
        return ($this->redis->hSet($key,$field,$value) === false) ? false : true;
    }

    public function setInfoFieldValueNx($field,$value){
        $key=$this->createInfoKey();
        return $this->redis->hSetNx($key,$field,$value) === false ?  false : true  ;
    }


    public function setInfoFieldNull($field){
        $key=$this->createInfoKey();
        return $this->redis->hSet($key,$field,Null) === false ?  false : true  ;
    }
    public function setInfoFieldJson($field,$value){
        $key=$this->createInfoKey();
        return $this->redis->hSetJson($key,$field,$value) === false ?  false : true  ;
    }


    public function setInfoFieldIncre($field,$value=1){
        $key=$this->createInfoKey();
        return $this->redis->hIncre($key,$field,$value)  ;
    }

    public function existsField($field){
        $key=$this->createInfoKey();
        return $this->redis->hExists($key,$field)  ;
    }

    protected function createInfoKey(){
        return $this->infoString.$this->infoId;
    }

    public function getInfoFieldValue($field){
        $key=$this->createInfoKey();
        return $this->redis->hGet($key,$field)  ;
    }
    public function getInfoFieldJson($field){
        $key=$this->createInfoKey();
        return $this->redis->hGetJson($key,$field)  ;
    }

    public function getInfoFieldNum(){
        $key=$this->createInfoKey();
        return $this->redis->hLan($key)  ;
    }

    public function getInfoList($array=[]){
        $key=$this->createInfoKey();
        return $this->redis->hGet($key,$array)  ;
    }

    public function removeField($field){
        $key=$this->createInfoKey();
        return $this->redis->hDel($key,$field)  ;
    }

    public function delete(){
        $key=$this->createInfoKey();
        return $this->redis->delete($key)  ;
    }


    /**
     * 字符串命名风格转换
     * type 0 将 Java 风格转换为 C 的风格 1 将 C 风格转换为 Java 的风格
     * @access public
     * @param  string  $name    字符串
     * @param  integer $type    转换类型
     * @param  bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public  function parseName($name, $type = 0, $ucfirst = true)
    {
        if (strpos($name,"get")===1){
            $name =  ltrim($name,"get");
        }
        if (strpos($name,"set")===1){
            $name =  ltrim($name,"set");
        }

        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);

            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }


    public function __destruct()
    {


    }


}
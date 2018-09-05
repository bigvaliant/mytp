<?php
/**
 * Created by PhpStorm.
 * User: ÎÒµÄµçÄÔ
 * Date: 2018/9/5
 * Time: 14:33
 */

namespace  app\base\service\base;

use app\base\service\center\OptionsCenter;
use app\base\service\center\RandNumCenter;
use think\Db;
use think\Request;

abstract class ServiceBase
{
    protected $model;
    protected $error;
    protected $optionNum;
    protected $timeString;
    protected $className;
    protected $functionName;
    protected $args;

    public function __construct()
    {
        $this->className = get_called_class();
        $this->optionNum = RandNumCenter::createOperateSerialNumber();
        $this->timeString = RandNumCenter::getTimeString();
        $this->_initialize();
    }

    abstract public function _initialize();

    protected function getInfoArray($map = [])
    {
        if (!isset($map["status"])) {
            $map["status"] = 1;
        }
        $result = $this->model->where($map)->find();
        if ($result) {
            return $result->toArray();
        } else {
            return [];
        }
    }

    protected function getInfoObject($map = [])
    {
        if (!isset($map["status"])) {
            $map["status"] = 1;
        }
        return $this->model->where($map)->find();
    }

    public function addError($error)
    {
        $this->error = is_string($error) ? $error : json_encode($error);
    }

    protected function getError()
    {
        return $this->error;
    }

    public function __destruct()
    {
        $operateData = [
            "number" => $this->optionNum,
            "class" => $this->className,
            "function" => $this->functionName,
            "args" => is_string($this->args) ? $this->args : json_encode($this->args),
            "error" => $this->error ? $this->error : null,
            "ip" => Request::instance()->ip(),
            "time" => $this->timeString,
        ];
        Db::table(OptionsCenter::$logServiceOperate)->insert($operateData);
    }
}
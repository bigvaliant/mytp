<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
 * Date: 2018/9/4
 * Time: 16:00
 */

namespace app\api\controller;


use app\base\controller\Base;
use think\Config;
use think\Exception;
use think\Log;

class Error extends Base
{
    // 空操作
    protected $api_config=[];
    public function _empty(){
        $action = $this->request->action();
        $this->api_config=Config::get("api_{$this->request->controller()}");
        if (isset($this->api_config[$action])){
            $action_name = $this->api_config[$action]['action_name'];
            //判断api_config中定义执行的方法是否存在
            if( method_exists($this,$action_name)){
                return $this->$action_name($action);
            }else{
                return self::showJsonReturnCodeWithOutData(1002);
            }
        }else{
            return self::showJsonReturnCodeWithOutData(1003,"参数错误");
        }
    }

    /**
     * 通用添加修改处理方法
     * @param $action //方法名称
     * @return array
     */
    protected function handleEditData($action){
        try{
            if($this->request->isPost()){
                throw new Exception("错误的提交方式");
            }
            if( ! isset($this->api_config[$action]['model_name'])  ){
                throw new Exception("错误的处理方式");
            }
            $param_list = isset($this->api_config[$action]['param_list'])
                ? $this->api_config[$action]['param_list']
                : false ;
            $model_name = $this->api_config[$action]['model_name'];
            $validate_name = isset($this->api_config[$action]['validate_name'])
                ? $this->api_config[$action]['validate_name']
                : false;
            return $this->editData($param_list,$validate_name,$model_name);
        }catch (Exception $e){
            Log::error($e->getMessage());
            return self::showJsonReturnCodeWithOutData(1008,$e->getMessage());
        }
    }
}
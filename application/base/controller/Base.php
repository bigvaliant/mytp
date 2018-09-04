<?php
namespace app\base\controller;

use think\Cache;
use think\Controller;
use think\Session;
use think\Loader;
abstract class Base extends Controller
{
    protected $error;             //出错时候的记录
    protected $log=[];            //要保存的记录
    protected $saveLog = false ;


    /**
     * @param string $code
     * @param array $data
     * @param string $msg
     * @return array
     */
    static public function showReturnCode($code = '', $data = [], $msg = '')
    {
        $return_data = [
            'code' => '500',
            'msg' => '未定义消息',
            'data' => $code == 1001 ? $data : [],
        ];
        if (empty($code)) return $return_data;
        $return_data['code'] = $code;
        if(!empty($msg)){
            $return_data['msg'] = $msg;
        }else if (isset(ReturnCode::$return_code[$code]) ) {
            $return_data['msg'] = ReturnCode::$return_code[$code];
        }
        return $return_data;
    }

    /**
     * 数据库字段 网页字段转换
     * #Date:
     * @param $array 转化数组
     * @return 返回数据数组
     */
    protected function buildParam($array)
    {
        $data=[];
        if (is_array($array)){
            foreach( $array as $item=>$value ){
                $data[$item] = $this->request->param($value);
            }
        }
        return $data;
    }

    static public function showReturnCodeWithOutData($code = '', $msg = '')
    {
        return self::showReturnCode($code,[],$msg);
    }

    /**
     * 快速修改
     * @param $array
     * @param bool|false $validate_name
     * @param string $model_name
     * @return array 返回code码
     */
    protected function editData($parameter = false, $validate_name = false, $model_name = false, $save_data = [])
    {
        if (empty($save_data)) {
            if ($parameter != false && is_array($parameter)) {
                $data = $this->buildParam($parameter);
            } else {
                $data = $this->request->post();
            }
        } else {
            $data = $save_data;
        }
        if (!$data) return $this->showReturnCode(1004);
        if ($this->checkLoginToken() && !isset($data['uuid'])) $data['uuid'] = $this->uuid;
        if ($validate_name != false) {
            $result = $this->validate($data, $validate_name);
            if (true !== $result) return $this->showReturnCodeWithOutData(1003,$result );
        }
        $model_edit = Loader::model($model_name);
        //dump($model_edit);
        if (!$model_edit) return $this->showReturnCode(1010);
        return $model_edit->editData($data);
    }

    protected function doModelAction($param_data,$validate_name = false, $model_name = false,$action_name='editData'){

        if ($validate_name != false) {
            $result = $this->validate($param_data, $validate_name);
            if (true !== $result) return $this->showReturnCodeWithOutData(1003,  $result);
        }
        $model_edit = Loader::model($model_name);
        if (!$model_edit) return $this->showReturnCode(1010);
        return $model_edit->$action_name($param_data);
    }

    protected function addLog($code='',$msg=''){
        $this->log[] =[
            'uuid' => $this->uuid,
            'url' => $this->request->url(true),
            'method' => $this->request->method(),
            'data' => $this->getData(),
            'ip' => $this->request->ip(),
            'code'=>$code,
            'desc' => $msg,
        ];
    }
    protected function toSaveLog(){
        $this->saveLog = true ;
        $this->addLog();
    }

    protected function showReturnCodeWithSaveLog($code = '', $data = [], $msg = ''){
        $this->saveLog = true ;
        $this->addLog($code,$msg);
        return self::showReturnCode($code, $data, $msg);
    }

    protected function getData(){
        if ($this->request->isPost()){
            return $this->request->post();
        }else{
            return $this->request->get();
        }
    }
    protected function saveLogAction(){
        if (!empty($this->log)){
            foreach($this->log as $value){
                dump($value);
            }
        }
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        //记录日志
        if (!empty($this->log) && $this->saveLog == true){
            $this->saveLogAction();
        }
    }
}
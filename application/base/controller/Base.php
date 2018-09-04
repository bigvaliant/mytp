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

    static public function showReturnCodeWithOutData($code = '', $msg = '')
    {
        return self::showReturnCode($code,[],$msg);

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
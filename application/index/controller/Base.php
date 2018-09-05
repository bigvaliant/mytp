<?php
namespace app\index\controller;

use app\base\controller\ReturnCode;

class Base extends \app\base\controller\Base
{
    protected $uuid='';
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

    /**
     * 获取用户信息
     * Power by Mikkle
     * @return array
     */
    private function getUid(){
        //数据库字段 网页字段转换
        $param = [
            'userid' => 'userid',
            'userpwd' => 'userpwd',
            'mobile' => 'mobile',
        ];
        $param_data = $this->buildParam($param);
        if (empty($param_data['userid'])&&empty($param_data['mobile'])) return self::showReturnCodeWithOutData(1003);
        $check_login = $this->doModelAction($param_data, 'base/Member.login', 'base/Member', 'checkLogin');
        if (!isset($check_login['code'])) $this->showReturnCodeWithSaveLog(1111);
        if ($check_login['code'] == 1001) {


        }
        return $check_login;

    }
}
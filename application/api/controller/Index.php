<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
 * Date: 2018/9/4
 * Time: 16:40
 */

namespace app\api\controller;


class Index extends Auth
{
    /**
     * 登陆验证
     * Power by Mikkle
     * QQ:776329498
     * @return array
     */
    public function login(){
        if ($this->request->isAjax()) {

            //数据库字段 网页字段转换
            $param = [
                'username' => 'username',
                'password' => 'password',
            ];
            $param_data = $this->buildParam($param);

            $check_login = $this->doModelAction($param_data, 'base/AdminUser.login', 'base/AdminUser', 'checkLogin');
            //记录错误信息
            if (!isset($check_login['code'])) $this->showReturnCodeWithSaveLog(1111);

            if ($check_login['code'] == 1001) {
                //设置全局登录
                $this->setLoginGlobal($check_login['data'], 1);
            }
            return $check_login;
        }else{
            return $this->fetch("login");
        }
    }
}
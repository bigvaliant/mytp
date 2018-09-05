<?php
/**
 * Created by PhpStorm.
 * User: �ҵĵ���
 * Date: 2018/9/4
 * Time: 16:40
 */

namespace app\api\controller;


class Index extends Auth
{
    /**
     * ��½��֤
     * Power by Mikkle
     * QQ:776329498
     * @return array
     */
    public function login(){
        if ($this->request->isAjax()) {

            //���ݿ��ֶ� ��ҳ�ֶ�ת��
            $param = [
                'username' => 'username',
                'password' => 'password',
            ];
            $param_data = $this->buildParam($param);

            $check_login = $this->doModelAction($param_data, 'base/AdminUser.login', 'base/AdminUser', 'checkLogin');
            //��¼������Ϣ
            if (!isset($check_login['code'])) $this->showReturnCodeWithSaveLog(1111);

            if ($check_login['code'] == 1001) {
                //����ȫ�ֵ�¼
                $this->setLoginGlobal($check_login['data'], 1);
            }
            return $check_login;
        }else{
            return $this->fetch("login");
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: �ҵĵ���
 * Date: 2018/9/4
 * Time: 16:14
 */

namespace app\api\controller;

use think\Cache;
use think\Cookie;
use think\Session;

class Base extends \app\base\controller\Base
{
    protected $loginType;  //�洢��½��Ϣ����  session  cache  redis
    protected $member_info;
    protected $isLogin;  //�ж��Ƿ��½
    protected $uuid;              //��½���UUID
    protected $config_list=[];

    /**
     * ����½��Ϣ
     * Power by Mikkle
     * QQ:776329498
     */
    public function _initialize()
    {
        if ($this->checkLoginGlobal()) {
            $this->isLogin = true;
        }
    }

    /**
     * ����Ƿ��¼
     * @return bool
     */
    public function checkLoginGlobal()
    {
        $check_success = false;
        switch ($this->loginType) {
            case 1;
            case "session";
                $this->uuid = Session::get('uuid', 'Global');
                $this->member_info = Session::get('member_info', 'Global');
                if ($this->uuid && $this->member_info) {
                    $check_success = true;
                }
                break;
            case 2;
            case "cache";
                $session_id_check = Cookie::get("session_id");
                $this->uuid = Cache::get("uuid_{$session_id_check}");
                $this->member_info = Cache::get("member_info_{$session_id_check}");
                if ($this->uuid && $this->member_info) {
                    $check_success = true;
                }
                //ˢ�� ������Ч��
                Cache::set("uuid_{$session_id_check}", $this->uuid);
                Cache::set("member_info_{$session_id_check}", $this->member_info);
                break;
            case 3:
            case "redis":

                break;
        }
        return $check_success;

    }

    /**
     * ����ȫ�ֵ�¼
     */
    public function setLoginGlobal($member_info = [], $login_code = 0)
    {
        $set_success = false ;
        if ($member_info) {
            switch ($this->loginType) {
                case 1:
                case "session":
                    Session::set('member_info', $member_info, 'Global');
                    Session::set('uuid', $member_info['uuid'], 'Global');
                    if ((Session::has("uuid", "Global"))) {
                        $set_success = true;
                    }
                    break;
                case 2:
                case "cache":
                    $session_id = $this->create_uuid("SN");
                    Cookie::set("session_id", $session_id);
                    Cache::set("member_info_$session_id", $member_info);
                    Cache::set("uuid_$session_id", $member_info['uuid']);
                    $session_id_check = Cookie::get("session_id");
                    if ((Cache::get("uuid_{$session_id_check}"))) {
                        $set_success = true;
                    }
                    break;
                case 3:case "redis":


                break;

            }
        }
        if (!$set_success) return false;
        //�����¼��¼
//        $this->saveLoginInfo($member_info['uuid'],$login_code);

        return true;
    }

    /**
     * ȫ���˳�
     * @return bool
     */
    protected function logoutGlobal(){
        switch ($this->loginType) {
            case 1:
            case "session":
                Session::delete('uuid', 'Global');
                Session::delete('member_info', 'Global');
                break;
            case 2:
            case "cache":
                $session_id_check = Cookie::get("session_id");
                Cache::rm("uuid_{$session_id_check}");
                Cache::rm("member_info_{$session_id_check}");
                Cookie::delete("session_id");
                break;
            case 3:case "redis":


            break;
        }
        $this->member_info = null;
        $this->uuid = null;
        return true;
    }

}
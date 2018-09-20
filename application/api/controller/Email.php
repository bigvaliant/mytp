<?php
/**
 * Created by nietai
 * 队列发送邮件.
 * User: 我的电脑
 * Date: 2018/9/6
 * Time: 16:29
 */

namespace app\api\controller;


use app\base\library\Ems;
use app\base\library\Queue;
use think\Exception;

class Email extends Auth
{
    public function _initialize()
    {
        parent::_initialize();
        \think\Hook::add('ems_send', function($params) {
            try{
                $jobData = ['email' => $params->email,'code'=>$params->code];
                $queue = Queue::set('email_code','emailCodeQueue',$jobData);
                if($queue !== false){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }catch (Exception $e){
                return FALSE;
            }
        });
    }

    public function sendEmailCode()
    {
        $ret = Ems::send('609848803@qq.com',NULL,'register');
        if ($ret)
        {
            echo '邮件入队成功';
        }
        else
        {
            echo '邮件入队失败';
        }
    }
}
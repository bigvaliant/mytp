<?php

namespace app\base\library;

use think\Log;
use think\Queue as tpQueue;
use think\Hook;

/**
 * 邮箱验证码类
 */
class Queue
{

    /**
     * 队列与类的映射
     * @var int 
     */
    protected static $options = [
        'email_code'  => 'app\base\job\EmailQueue@emailCode',//发送邮箱验证码队列
        'email_verify'  => 'app\base\job\EmailQueue@emailVerify',//发送验证邮件队列
    ];

    /**
     * 最大允许检测的次数
     * @var int 
     */
    protected static $maxCheckNums = 10;

    /**
     * 发送数据到队列
     *
     * @param   string       $queueInfo   队列标示
     * @param   string    $queueName    队列名称
     * @param   $data
     * @return  bool
     */
    public static function set($queueInfo, $queueName,$data=[])
    {
        if(empty($queueInfo) || empty($queueName)){
            return false;
        }

        $queueClassName = isset(self::$options[$queueInfo])? self::$options[$queueInfo]:false;
        if(!$queueClassName){
            return false;
        }

        try{
            $isPushed = tpQueue::push($queueClassName,$data,$queueName);

            if($isPushed !== false){
                return true;
            }else{
                return false;
            }
        }catch (\Exception $e){
            Log::error($e->getMessage().'->'.$queueClassName);
            return false;
        }

    }

    /**
     * 获取最后一次邮箱发送的数据
     *
     * @param   int       $email   邮箱
     * @param   string    $event    事件
     * @return  Ems
     */
    public static function get($email, $event = 'default')
    {
//        $ems = \app\common\model\Ems::
//                where(['email' => $email, 'event' => $event])
//                ->order('id', 'DESC')
//                ->find();
//        Hook::listen('ems_get', $ems, null, true);
//        return $ems ? $ems : NULL;
    }

}

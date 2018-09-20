<?php
/**
 * Created by PhpStorm.
 * User: �ҵĵ���
 * Date: 2018/9/7
 * Time: 8:59
 */

namespace app\worker\controller;


use mikkle\tp_master\Log;
use mikkle\tp_wechat\Wechat;

class WechatMessage extends Base
{

    protected $options=[];

    protected $wechat;
    protected $listName;
    public function __construct($options=[])
    {
        $this->options = empty($options) ? Config::get("wechat.erp_options") : $options;
        parent::__construct($options);

        $this->message=Wechat::message($this->options);
        $this->listName = md5($this->workerName);
    }


    /**
     * ����������Ƿ�ִ����
     * Power: Mikkle
     * Email��776329498@qq.com
     * @return bool
     */
    static public function checkCommandRun(){
        return self::redis()->get("command") ? true :false;
    }

    /**
     * �������ģ����Ϣ����
     *
     * ��������δ���� ֱ��ִ��
     * Power: Mikkle
     * Email��776329498@qq.com
     * @param $data
     * @param array $options
     */
    static public function add($data,$options=[]){
        $instance = self::instance($options);
        switch (true){
            case (self::checkCommandRun()):
                $instance->redis->lpush($instance->listName,$data);
                $instance->runWorker();
                break;
            default:
                $instance->message->sendTemplateMessage($data);
        }
    }

    /**
     * ������ִ�еķ���
     * Power: Mikkle
     * Email��776329498@qq.com
     */
    static public function run(){
        $instance = self::instance();
        try{
            $i = 0;
            while(true){
                $data = $instance->redis->rpop($instance->listName);
                if ($data){
                    $re=$instance->sendMessage($data);
                    Log::notice($re);
                }else{
                    break;
                }
                $i++;
                sleep(1);
            }
            $instance->clearWorker();
            echo "ִ����{$i}������".PHP_EOL;
        }catch (\Exception $e){
            Log::error($e);
            $instance->clearWorker();
            die($e->getMessage());
        }
    }

    /**
     * ����ģ����Ϣ�ķ���
     * Power: Mikkle
     * Email��776329498@qq.com
     * @param $data
     * @return bool
     */
    protected function sendMessage($data){

        $no = $this->message->sendTemplateMessage($data);
        if ($no){
            Log::notice("���ͳɹ�[{$no}]");
            return true ;
        }else{
            Log::notice("����ʧ��[{$no}]");
            $this->failed($data);
        };

    }

    /**
     * ����ִ�еĻص�����
     * Power: Mikkle
     * Email��776329498@qq.com
     * @param $data
     */
    protected function failed($data){
    }

}
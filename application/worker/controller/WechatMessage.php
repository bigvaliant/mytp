<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
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
     * 检测命令行是否执行中
     * Power: Mikkle
     * Email：776329498@qq.com
     * @return bool
     */
    static public function checkCommandRun(){
        return self::redis()->get("command") ? true :false;
    }

    /**
     * 快速添加模版消息任务
     *
     * 当命令行未运行 直接执行
     * Power: Mikkle
     * Email：776329498@qq.com
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
     * 命令行执行的方法
     * Power: Mikkle
     * Email：776329498@qq.com
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
            echo "执行了{$i}次任务".PHP_EOL;
        }catch (\Exception $e){
            Log::error($e);
            $instance->clearWorker();
            die($e->getMessage());
        }
    }

    /**
     * 发送模版消息的方法
     * Power: Mikkle
     * Email：776329498@qq.com
     * @param $data
     * @return bool
     */
    protected function sendMessage($data){

        $no = $this->message->sendTemplateMessage($data);
        if ($no){
            Log::notice("发送成功[{$no}]");
            return true ;
        }else{
            Log::notice("发送失败[{$no}]");
            $this->failed($data);
        };

    }

    /**
     * 出错执行的回调方法
     * Power: Mikkle
     * Email：776329498@qq.com
     * @param $data
     */
    protected function failed($data){
    }

}
<?php
namespace app\worker;


use mikkle\tp_master\Log;
use mikkle\tp_worker\TimingWorkerBase;

class Time extends TimingWorkerBase
{
    /**
     * Created by Nietai.
     * Info: 定时执行队列
     * Date: 2018/9/7
     * Time: 13:36
     */
    protected function runHandle($data)
    {
        //do something
        Log::notice(  "执行定时任务测试" );
    }
}
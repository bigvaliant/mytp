<?php
namespace app\worker;


use mikkle\tp_master\Log;
use mikkle\tp_worker\TimingWorkerBase;

class Time extends TimingWorkerBase
{
    /**
     * Created by Nietai.
     * Info: ��ʱִ�ж���
     * Date: 2018/9/7
     * Time: 13:36
     */
    protected function runHandle($data)
    {
        //do something
        Log::notice(  "ִ�ж�ʱ�������" );
    }
}
<?php
namespace app\api\controller;

use app\worker\auto\Cycle;
use think\Hook;

class Worker extends Auth
{
    /**
     * Created by nietai.
     * info: 异步多进程队列
     * Date: 2018/9/7
     * Time: 9:57
     */
    public function testMultiProcess()
    {
        $data =[
            "name"=>"mikkle"
        ];
        $Result =  \app\worker\Test::add( $data);
        $data =[
            "name"=>"nietai"
        ];
        $Result1 =  \app\worker\Test1::add( $data);
        return $Result;
    }

    /**
     * 异步定时任务
     */
    public function timeTest()
    {
        //要执行定时任务的参数
        $data = ["name"=>"mikkle",];
        //多少秒后执行 或者 要执行的时间戳
        $runTime = 30 ;
        \app\worker\Time::add($data,$runTime);
        //或者 要执行的时间戳
        $runTime = time()+60 ;
        \app\worker\Time::add($data,$runTime);
    }

    /**
     * 异步钩子任务
     */
    public function hookTest()
    {
//        $data = array();
//        if($data){
//            dump('yes');
//        }else{
//            dump('no');
//        }
        $params = array('hook'=>'asd');
        $options = array('option'=>'asd');
        Hook::listen('hook_test',$params);
    }

    //异步循环队列任务开始
    public function cycleStart()
    {
        $data =[
            "name"=>"nietai"
        ];
        return Cycle::start($data);
    }

    //结束异步循环队列任务
    public function cycleStop()
    {
        return Cycle::stop();
    }

    //查看异步循环队列任务状态
    public function cycleStatus()
    {
        return Cycle::status();
    }
}
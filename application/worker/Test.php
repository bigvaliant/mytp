<?php
namespace app\worker;

use mikkle\tp_master\Log;
use mikkle\tp_worker\WorkerBase;

class Test extends WorkerBase
{
    /**
     * Created by Nietai.
     * Info: 需要执行的进程任务
     * Date: 2018/9/7
     * Time: 9:48
     */
   protected function runHandle($data)
   {
       for($i=1;$i<10;$i++){
           $this->sleep(1);
           echo '睡眠一秒';
       }

       Log::notice( "测试" );
   }
}
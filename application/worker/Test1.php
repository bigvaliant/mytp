<?php
namespace app\worker;

use mikkle\tp_master\Log;
use mikkle\tp_worker\WorkerBase;

class Test1 extends WorkerBase
{
    /**
     * Created by Nietai.
     * Info: ��Ҫִ�еĽ�������
     * Date: 2018/9/7
     * Time: 9:48
     */
   protected function runHandle($data)
   {
       dump($data);
       for($i=1;$i<10;$i++){
           $this->sleep(1);
           echo '˯��һ��';
       }

       Log::notice( "����1" );
   }
}
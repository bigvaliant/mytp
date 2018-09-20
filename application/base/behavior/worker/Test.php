<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
 * Date: 2018/9/7
 * Time: 14:04
 */

namespace app\base\behavior\worker;

use mikkle\tp_worker\WorkerHookBase;
use mikkle\tp_master\Log;

class Test extends WorkerHookBase
{
//    public function hookTest(&$params,$options)
//    {
//        dump($params);
//        dump($options);
//    }
    protected function runHandle($data)
    {

        for ($i=0 ; $i<10;$i++){
            $this->sleep(1);
            echo '睡眠一秒';
        }
        Log::notice(  "测试钩子"  );
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Mikkle
 * QQ:776329498
 * Date: 2018/2/23
 * Time: 16:20
 */

namespace app\worker\auto;

use mikkle\tp_worker\CycleWorkBase;
use mikkle\tp_tools\Curl;
use think\Exception;
use think\Log;

class Cycle extends CycleWorkBase
{
    protected function runCycleHandle($data)
    {

    }

    protected function runHandle($data)
    {
        //windows�첽������ʾ��
        try{
            $i=0;
            while ( true ){
                self::signWorking();
//                Curl::get( "http://www.mikkle.cn/");
                echo "ѭ��ִ�г���{$i}��\n";
                $time = $this->getNextRunTime();
                $this->sleep($time);
                $i++;
                Log::notice("ѭ��ִ�г���ִ�г���".time() );
                if ( $this->checkWorkingStop() ){
                    break;
                }
            }
            $this-> clearWorkingWork();
            Log::notice("ѭ��ִ�г���ִ����");
        }catch (Exception $e){
            Log::notice( $e ->getMessage());
        }
    }

    protected function getNextRunTime(){
        if (time()<strtotime( "02:00") ){
            return 15;
        }elseif(time()<strtotime( "09:57") ){
            return 180;
        }elseif(time()<strtotime( "22:00") ){
            return 3;
        }elseif(time()<strtotime( "23:59") ){
            return 15;
        }
        return 60;
    }
}
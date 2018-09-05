<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
 * Date: 2018/9/4
 * Time: 15:14
 */

namespace app\api\controller;

use mikkle\tp_excel\Excel;

class Test extends Auth
{
    public function test(){
        //检验用户是否登录
        if (!$this->uuid){
            return self::showReturnCodeWithOutData(1004);
        }

        //使用用户信息
        dump($this->member_info);
    }

    /**
     * 导出excel表
     */
    public function downloadExl()
    {
        $excel=new Excel();
        $table_name="my_admin_user";
        $field=["id"=>"序号","name"=>"用户名","nickname"=>"用户昵称"];
        $map=["status"=>1];
        $map2=["status"=>-1];

        $excel->setExcelName("下载装修项目")
            ->createSheet("装修项目",$table_name,$field,$map)
//            ->createSheet("已删除装修项目",$table_name,$field,$map2)
            ->downloadExcel();
    }
}
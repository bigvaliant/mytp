<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
 * Date: 2018/9/4
 * Time: 15:14
 */

namespace app\api\controller;

use app\base\model\TestHash;
use mikkle\tp_excel\Excel;
use mikkle\tp_redis\Redis;

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

    public function testRedis()
    {
        $redis=Redis::instance(["index"=>0]);
        $redis_1=Redis::instance(["index"=>1]);
        $redis_2=Redis::instance(["index"=>2]);
        $redis->set("name","This is mikkle's redis");
        $redis_1->set("name","This is mikkle's redis_1");
        $redis_2->set("name","This is mikkle's redis_2");
        dump( $redis->get("name"));
        dump( $redis_1->get("name"));
        dump( $redis_2->get("name"));
        dump($redis);
        dump($redis_1);
        dump($redis_2);

    }

    /**
     * model与redishash结合
     */
    public function RedisModelHash(){

//        $model = new TestHash();
//        $data= [
//            'name'=>'Mikkle\' RedisHash',
//            "hash_edit"=>'要修改的字段',
//        ];
//        echo "添加的数据".PHP_EOL;
//        dump($data);
//        $model->save($data);
//        $id  = $model->id;
//        echo "添加Id为 $id ".PHP_EOL;
//        echo "添加后查询Hash数据为".PHP_EOL;
//        dump($model->RedisHash->setKey(3 )->get());
//        dump($model->RedisHash->exists($hash_key_value)
        $test = TestHash::quickGet(4);
        dump($test);
//        $model->data(['hash_edit'=>'这个字段修改了4',])->isUpdate(true,['id'=>3 ])->save();
//        echo "修改后查询Hash数据为".PHP_EOL;
//        dump($model->RedisHash->setKey(3)->get());
//        echo "添加后Hash库所有 key列表".PHP_EOL;
//        dump($model->RedisHash->keys("*"));
//        $model->destroy(3 );
//        echo "删除后Hash库所有 key列表".PHP_EOL;
//        $model->RedisHash->clearAll();
//        dump($model->RedisHash->keys("*"));
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
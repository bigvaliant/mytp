<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
 * Date: 2018/9/4
 * Time: 10:01
 */

namespace app\index\controller;


use app\index\model\Orders;

class Test extends Base
{
    public function index()
    {
        return json($this->showReturnCodeWithSaveLog(1001,'演示析构函数成功了'));
    }
}
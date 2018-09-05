<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
 * Date: 2018/9/4
 * Time: 14:34
 */

namespace app\base\validate;


use think\Validate;

class AdminUser extends Validate
{
    protected $rule = [
        ['name','require|max:25|unique:admin_user','用户名必须|用户名最多不能超过25个字符|用户名已存在'],
    ];

    //场景不同场景验证不同的字段
    protected $scene = [
        'edit'=>['name']
    ];
}
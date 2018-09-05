<?php
namespace app\api\controller;

use app\base\controller\Base;
use app\base\library\DataEdit;
use think\Controller;

class AdminUser extends Base
{
    public function adminUserInfo($id=1){
        return model('base/AdminUser')->find($id);
    }

    public function changeJob()
    {
        $data = array(
            'name'=>'admin',
            'id'=>2
        );

        $validate_name='base/AdminUser.edit';
        $model_name='base/AdminUser';
        return  json($this->editData(false,$validate_name,$model_name,$data));
    }

    public function adminUserWithDepartment(){
        $open_id='oO059v39zsst76IkuiYV3yMvc4Sw';
        $user_info=model('base/AdminUser')->getInfoByOpenid($open_id);
        return $user_info->toArray();
    }

    public function edittest()
    {
        $data = [
            "name" => "admina",
        ];
        $validate_name = "base/AdminUser";
        $model_name = 'base/AdminUser';
        $editData = DataEdit::instance();

        $re = $editData
            ->setData($data)
            ->setAppend(["append" => "this is append"])
            ->setValidate($validate_name)
            ->setModel($model_name)
            ->save();
        dump($editData->getError());
    }

}
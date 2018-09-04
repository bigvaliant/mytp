<?php
namespace app\api\controller;

use think\Controller;

class AdminUser extends Controller
{
    public function adminUserInfo($id=1){
        return model('base/AdminUser')->find($id);
    }

    public function adminUserWithDepartment(){
        $open_id='oO059v39zsst76IkuiYV3yMvc4Sw';
        $user_info=model('base/AdminUser')->getInfoByOpenid($open_id);
        return $user_info->toArray();
    }

}
<?php
namespace app\base\model;

use think\Model;

class AdminUser extends Base
{
    protected $name = "adminUser";

    public function getInfoByOpenid($open_id){
        return $this->where('weixin_openid',$open_id)->find();
    }
    //����һ
    public function getDepartmentIdAttr($value,$data)
    {
        return $this->belongsTo('AdminDepartment')->where('id',$value)->value('name');
    }

    public function getRoleIdAttr($value,$data)
    {
        return $ids = $this->belongsTo('AdminNode')->where('id','in',$this->belongsTo('AdminRole')->where('id',$value)->value('rule'))->select();
    }

//    public function getRoleIdAttr($value,$data)
//    {
//        $ids = $this->belongsTo('AdminRole')->where('id',$value)->value('rule') ; return $this->belongsTo('AdminNode')->where('id','in',$ids )->select();
//    }


//    //������
//    public function getDepartmentIdAttr($value,$data)
//    {
//        return $this->belongsTo('AdminDepartment','department_id','id')->where('id',$data['department_id'])->value('name');
//    }
}
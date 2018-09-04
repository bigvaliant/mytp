<?php
namespace app\base\model;

use think\Model;

class Base extends Model
{
    //默认密碼数据加密KEY
    static private $dataAuthKey = 'SystemPowerByMikkle';

    static public function getgiMd5Password($password)
    {
        return md5(md5($password) . self::$dataAuthKey);
    }

    static public function createUuid($baseCode = '')
    {
        $baseCode = empty($baseCode) ? "UU" : $baseCode;
        $uuid = $baseCode . strtoupper(uniqid()) . self::builderRand(6);
        return $uuid;
    }
    /**
     * 根据有Id修改信息 无Id 新增信息
     * @param $data
     * @return false|int|string
     * @throws
     */
    public function editData($data){
        if (isset($data['id'])){
            if (is_numeric($data['id']) && $data['id']>0){
                $save = $this->allowField(true)->save($data,[ 'id' => $data['id']]);
            }else{
                $save  = $this->allowField(true)->save($data);
            }
        }else{
            $save  = $this->allowField(true)->save($data);
        }
        if ( $save == 0 || $save == false) {
            $res=[  'code'=> 1009,  'msg' => '数据更新失败', ];
        }else{
            $res=[  'code'=> 1001,  'msg' => '数据更新成功',  ];
        }
        return $res;
    }


    /**
     * 创建随机数
     * @param int $num  随机数位数
     * @return string
     */
    static public function builderRand($num=8){
        return substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $num);
    }

    /*
    * 分转成元
    */
    static public function CNYFenToYuan($fen){
        return sprintf("%.2f", ($fen/100) );
    }
}
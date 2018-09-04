<?php
namespace app\base\model;

use think\Model;

class Base extends Model
{
    //Ĭ���ܴa���ݼ���KEY
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
     * ������Id�޸���Ϣ ��Id ������Ϣ
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
            $res=[  'code'=> 1009,  'msg' => '���ݸ���ʧ��', ];
        }else{
            $res=[  'code'=> 1001,  'msg' => '���ݸ��³ɹ�',  ];
        }
        return $res;
    }


    /**
     * ���������
     * @param int $num  �����λ��
     * @return string
     */
    static public function builderRand($num=8){
        return substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $num);
    }

    /*
    * ��ת��Ԫ
    */
    static public function CNYFenToYuan($fen){
        return sprintf("%.2f", ($fen/100) );
    }
}
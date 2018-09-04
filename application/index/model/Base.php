<?php
namespace app\index\model;

use think\Model;

class Base extends Model
{
    //Ĭ���ܴa���ݼ���KEY
    static private $dataAuthKey = 'SystemPowerByMikkle';

    static public function getMd5Password($password)
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
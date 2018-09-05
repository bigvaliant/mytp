<?php
/**
 * Created by PhpStorm.
 * User: �ҵĵ���
 * Date: 2018/9/4
 * Time: 16:48
 */

namespace app\base\model;


use think\Db;
use think\Exception;
use think\Log;
use think\Session;

abstract class BaseLeGuan extends Base
{
    protected $versionField="edit_version";
    /**
     * �����ֹ������޸�
     * Power: Mikkle
     * Email��776329498@qq.com
     * @param $save_data�������޸ĵ�����
     * @param string $edit_pk  �޸ĵģɣ��ֶ�����
     * @param string $version_field�����ֹ����汾���ֶ�����
     * @return array
     */
    public function editDateWithLock($save_data,$edit_pk="",$version_field=""){
        if (empty($version_field)){
            $version_field = isset($this->versionField) ? $this->versionField : "edit_version";
        }
        if (empty($edit_pk)){
            $edit_pk = isset($this->editPk) ? $this->editPk : $this->getPk();
        }
        //�ж�PK�ֶ��Ƿ����
        if (!isset($save_data[$edit_pk])||!isset($save_data[$version_field])){
            return self::showReturnCodeWithOutData(1003,"����ȱʧ");
        }else{
            $map[$edit_pk] = $save_data[$edit_pk];
            $map[$version_field] = $save_data[$version_field];
            //�޳�PK
            unset($save_data[$edit_pk]);
        }
        try{
            //���汾�ֶ�
            if($this->hasColumn($version_field)){
                throw new Exception("�ֹ����汾�ֶ�[$version_field]������");
            }
            $original_data = $this->where($map)->find();
            if (empty($original_data)){
                throw new Exception("������Ϣ�Ѿ��䶯��,�����²���!");
            }
            foreach ($save_data as $item=>$value){
                if (isset($original_data[$item])){
                    //�޸ĵ���ֵ����ʱ�� �޳�
                    if ($original_data[$item]==$value){
                        unset( $save_data[$item]);
                    }elseif($item!=$version_field){
                        unset( $original_data[$item]);
                    }
                }else{
                    //�޸ĵ��ֶβ����� �޳�
                    unset( $save_data[$item]);
                }
            }
            if(empty($save_data)){
                throw new Exception("�޸ĵ���ֵ�ޱ仯");
            }
            //�汾������
            $save_data[$version_field]=(int)$original_data[$version_field]+1;
            if (1!=$this->allowField(true)->save($save_data,$map)){
                throw new Exception("�޸���Ϣ����:".$this->getError());
            }
            //��¼�޸���־
            $this->saveEditLog($original_data,$save_data);
            return self::showReturnCodeWithOutData(1001);
        }catch (Exception $e){
            $msg=$e->getMessage();
            return self::showReturnCodeWithOutData(1003,$msg);
        }
    }

    /**
     * �����޸���Ϣ
     * Power: Mikkle
     * Email��776329498@qq.com
     * @param $original_data
     * @param $save_data
     * @return bool
     */
    protected function saveEditLog($original_data,$save_data){
        if (empty($original_data)&&empty($save_data)){
            $this->error="������޸���Ϣ������";
            return false;
        }
        $log_data=[
            "uuid"=>Session::get('uuid', 'Global'),
            "model_data"=>$this->name,
            "original_data"=>$original_data,
            "save_data"=>$save_data,
            "update_time"=>time(),
        ];
        try{
            Db::table("update_log")->insert($log_data);
            return true;
        }catch (Exception $e){
            $log_data["error"]="�����޸���Ϣ����";
            Log::write(json_encode($log_data),"error");
            return false;
        }
    }
}
<?php
namespace app\base\model;

use think\Db;
use think\Exception;
use think\Log;
use think\Model;
use think\Session;

abstract class Base extends Model
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
    public function create_uuid($baseCode='')
    {
        return self::createUuid($baseCode);
    }

    /**
     * 通过查询条件获取单条数据对象
     * @param array $map  查询条件
     * @param bool|true $field 字段
     * @param array $append 追加已经定义获取器字段
     * @param bool|true $status
     * @return $this|array|false|\PDOStatement|string|Model
     */
    public function getInfoByMap($map=[],$field=true,$append=[],$status=true){
        if($status&&!isset($map['status'])){
            $map['status']=1;
        }
        $object = $this->where($map)->field($field)->find();
        if(!empty($object)&&!empty($append)){
            return $object->append($append);
        }else{
            return $object;
        }
    }

    /**
     * 通过查询条件获取单条数据(数组)
     * @param array $map
     * @param bool|true $field
     * @param array $append
     * @param bool|true $status
     * @return array
     */
    public function getArrayByMap($map=[],$field=true,$append=[],$status=true){
        if($status&&!isset($map['status'])){
            $map['status']=1;
        }
        $object = $this->where($map)->field($field)->find();
        if(!empty($object)&&!empty($append)){
            $return = $object->append($append);
        }else{
            $return = $object;
        }
        return empty($return) ? [] : $return->toArray();
    }

    /**
     * 通过查询条件获取多条数据(数组)
     * @param array $map
     * @param bool|true $field
     * @param array $append 这需要在模型里增加获取器
     * @param bool|true $status
     * @return array
     */
    public function getListByMap($map=[],$field=true,$append=[],$status=true){
        if($status&&!isset($map['status'])){
            $map['status']=1;
        }
        $object_list = $this->where($map)->field($field)->select();
        $list=[];
        if(!empty($object_list)){
            foreach($object_list as $item=>$value){
                if(!empty($append)){
                    $list[]= $value->append($append)->toArray();
                }else{
                    $list[]= $value->toArray();
                }
            }
        }
        return $list;
    }
    /**
     * 判断字段是否存在
     * Power: Mikkle
     * Email：776329498@qq.com
     * @param $column
     * @param string $table
     * @return bool
     */
    protected function hasColumn($column,$table=""){
        $table = isset($table)?$table:$this->table;
        if (empty($table)||$column){
            $this->error="hasColumn方法参数缺失";
            return false;
        }
        $sql = "SELECT * FROM information_schema.columns WHERE table_schema=CurrentDatabase AND table_name = '{$table}' AND column_name = '{$column}'";
        return $this->query($sql) ? true : false;
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

    static public function showReturnCode($code = '', $data = [], $msg = ''){
        return \app\base\controller\Base::showReturnCode($code, $data, $msg);
    }
    static public function showReturnCodeWithOutData($code = '', $msg = '')
    {
        return \app\base\controller\Base::showReturnCode($code, [], $msg);
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
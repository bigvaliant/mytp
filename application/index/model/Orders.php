<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
 * Date: 2018/9/4
 * Time: 10:38
 */

namespace app\index\model;

use think\Db;
use think\Model;

class Orders extends Base
{
    protected $table = "my_orders";
    protected $name = "orders";
    protected $pk = "id";
    protected $insert = ['status'=>1,'guid','order_no','order_state'=>0,'pay_type'=>0,'send_state'=>0,'is_comment'=>0,'factory_state'=>0];
    protected $autoWriteTimestamp = true;

    /**
     * 获取器
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getDistributeAttr($value, $data){
        $get_data = ['SF'=>'顺丰快递','ZT'=>'中通快递'];
        return $get_data[$value];
    }

    public function getPayTimeAttr($value, $data){
        return date('Y-m-d h:i:s', $value);
    }

    public function getPayTypeAttr($value, $data){
        if ($value){
            $get_data = ['WxPay'=>'微信支付','AliPay'=>'支付宝支付'];
            return isset($get_data[$value]) ? $get_data[$value] : '其他方式';
        }else{
            return $value;
        }

    }

    public function getIsPayTextAttr($value, $data){
        $get_data = ['0'=>'未付款','1'=>'已付款'];
        return $get_data[$data['is_pay']];
    }

    protected function setGuidAttr($value, $data)
    {
        return $this->create_uuid() ;
    }


    /**
     * 订单添加
     * 含有关联添加和事务回滚
     * Power by Mikkle
     * QQ:776329498
     * @param $data
     * @return array
     */
    public function addOrder($data)
    {
        if (!isset($data['consignee_id'])) return ['code' => '1003', 'msg' => '地址信息不存在'];
        $consignee = $this->table('my_user_address')->where(['guid' => $data['consignee_id'], 'status' => 1])->find();
        if (!$consignee) return ['code' => '1003', 'msg' => '地址信息不存在'];
        $cart_list = json_decode($data['cart_list']);
        if (!is_array($cart_list) || !$cart_list) return ['code' => '1003', 'msg' => '订单商品信息不存在'];
        // 计算总金额
        $goods_money = 0;
        foreach ($cart_list as $cart_key => $cart_value) {
            if (is_object($cart_value)) {
                $goods = $this->table('v_guid_all')->where('my_guid', $cart_value->cart_id)->find();
                $goods_num = $cart_value->num;
            } elseif (is_array($cart_value)) {
                $goods = $this->table('my_guid_all')->where('my_guid', $cart_value['cart_id'])->find();
                $goods_num = $cart_value['num'];
            } else {
                $goods = [];
            }
            if (!$goods) return ['code' => '1003', 'msg' => '商品信息不存在'];
            $data_order[$cart_key]['my_guid'] = $goods['my_guid'];
            $data_order[$cart_key]['my_price'] = $goods['my_price'];
            $data_order[$cart_key]['my_num'] = $goods_num;
            $goods_money = $goods_money + $goods['my_price'] * $goods_num;
        }
        $order_data['uuid'] = $data['uuid'];
        $order_data['amount'] = $goods_money;
        $order_data['order_num'] = count($data_order);
        $order_data['distribute'] = $data['distribute'];
        $order_data['order_state'] = 0;
        $order_data['expiration_time'] = time()+60*60*24*2;
        $order_data['order_desc'] = $data['remark'];
        $order_data['consignee_name'] = $consignee['consignee_name'];
        $order_data['consignee_mobile'] = $consignee['consignee_mobile'];
        $order_data['consignee_address'] = $consignee['province_name'] . ' ' . $consignee['area_name'] . ' ' . $consignee['district_name'] . ' ' . $consignee['consignee_address'];

        try{
            $this->startTrans();
            $this->data($order_data)->isUpdate(false)->save();
            $new_order = $this->order_no;
            if (!is_numeric($new_order)) throw new \Exception("商品订单添加失败");
            $this->hasMany('OrdersAccess', 'order_no', 'order_no')->saveAll($data_order);
            $this->commit();
            return ['code' => '1001', 'msg' => '商品订单添加成功', 'data' => ['order_no' => $new_order, 'cart_list' => $data_order]];

        }catch (\Exception $e){
            $this->rollback();
            return ['code'=>'1008','msg'=>'商品订单添加失败','data'];
        }


    }

    /**
     * 订单类修改器 含防重复筛查
     * @param $value
     * @param $data
     * @return string
     */
    protected function setOrderNoAttr($value, $data)
    {
        do {
            $order_no= date('Ymd').$this->builderRand();
        } while ($this->where('order_no',$order_no)->count()>0); //为了防止订单号的重复，这里使用一次查库过滤
        return $order_no ;
    }

    /**
     * 获取订单列表 含append用法
     * @param array $map
     * @param string $order
     * @param int $limit
     * @param int $page
     * @return array
     */
    public function getOrderList($map=[],$order = 'id desc',$limit=8,$page=1){

        $field='order_no,order_num,amount,distribute,create_time,is_pay,send_state,order_state';
        $map['status']=1;
        $data=$this->where($map)->field($field)
            ->limit($limit)
            ->page($page)
            ->order($order)->select();
        $re=[];
        foreach($data as $order){
            $re[]= $order->append(['cover_info'])->toArray();
        }
        return $re;
    }

    /**
     * 订单详情
     * Power by Mikkle
     * QQ:776329498
     * @param array $map
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function getOrderInfo($map=[]){
        if (!$map) return false;
        $field = 'order_no,amount,distribute,order_desc,consignee_name,consignee_mobile,consignee_address,send_state,is_pay,is_comment,order_state,create_time';
        $data=$this->where($map)->field($field)->find();
        if (!$data) return false;
        //$data['cart_list']=$data->hasManyThrough('VGuidAll','OrdersAccess','order_no','my_guid','order_no')->select();
        $data['cart_list']=$this->getOrderAccessList($data['order_no']);
        return $data;
    }

    /**
     * 订单简要信息
     * @param $order_no
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function getOrderSimpleInfo($order_no){
        if (!$order_no) return false;
        $map['order_no']=$order_no;
        $field = 'order_no,amount,distribute,order_desc,consignee_name,consignee_mobile,consignee_address,create_time';
        $data=$this->where($map)->field($field)->find();
        if (!$data) return false;
        return $data;
    }


    /*
     * 关联
     */
    public function orderAccess(){
        $this->hasMany('OrdersAccess','order_no','order_no');
    }
    /**
     * 视图查询
     * @param $order_no
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getOrderAccessList($order_no){
        return Db::view('my_orders_access','id,order_no,my_guid,my_num,my_price,is_comment')
            ->view('my_guid_all','my_name,my_type,my_price,my_pic','my_orders_access.my_guid=my_guid_all.my_guid')
            ->view('my_common_picture','path','my_common_picture.id=my_guid_all.my_pic')
            ->where('order_no',$order_no)
            ->cache('OrderList_'.$order_no)
            ->select();
    }

    public function getBatchNoOrderList($factory_batch,$order = 'id desc'){

        $data =  $this->view('my_orders','order_no ,is_pay,pay_type,pay_time,order_num,amount,distribute,create_time')
            ->view('my_factory_batch','batch_no','my_orders.factory_batch=my_factory_batch.batch_no')
            ->where('my_factory_batch.id',$factory_batch)
            //->cache('getBatchNoOrderList_'.$factory_batch)
            ->order('my_orders.id desc')
            ->select();
        $re=[];

        foreach($data as $order){

            $re[]= $order->toArray();
        }
        return $re;
    }
    public function getOrderInfoForExpressByOrderNo($order_no){
        if (empty($order_no)) return false;
        $field = 'guid,order_no,send_state,consignee_name,consignee_mobile,consignee_address,order_desc';
        $map = ['is_pay'=>1,'status'=>1];
        return $this->where($map)->field($field)->where(['order_no'=>$order_no])->find()->toArray();
    }
}
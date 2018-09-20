<?php
/**
 * Created by PhpStorm.
 * User: �ҵĵ���
 * Date: 2018/9/7
 * Time: 8:57
 */

namespace app\worker\controller;


use mikkle\tp_redis\Redis;
use think\Config;

abstract class Base
{
    protected $redis;
    protected $workList;
    protected $workerName;
    public static $instance;

    /**
     * Base constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->redis = $this->redis();
        $this->workList = "worker_list";
        $this->workerName = get_called_class();
    }


    /**
     * redis�����Զ���Redis��
     * Power: Mikkle
     * Email��776329498@qq.com
     * @param array $options
     * @return Redis
     */
    protected static function redis($options = [])
    {
        $options = empty($options) ? $redis = Config::get("command.redis") : $options;
        return Redis::instance($options);
    }


    /**
     * ��ע������ִ�д�����
     * Power: Mikkle
     * Email��776329498@qq.com
     */
    public function runWorker()
    {
        $this->redis->hset($this->workList, $this->workerName, $this->workerName);
    }

    /**
     * ��ע���������������
     * Power: Mikkle
     * Email��776329498@qq.com
     */
    public function clearWorker()
    {
        $this->redis->hdel($this->workList, $this->workerName);
    }


    /**
     * Power: Mikkle
     * Email��776329498@qq.com
     * @param array $options
     * @return static
     */
    static public function instance($options = [])
    {
        if (isset(self::$instance)) {
            return self::$instance;
        } else {
            return new static($options);
        }
    }
}
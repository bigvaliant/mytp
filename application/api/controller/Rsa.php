<?php
/**
 * Created by PhpStorm.
 * User: 我的电脑
 * Date: 2018/9/7
 * Time: 14:41
 */

namespace app\api\controller;


use think\Config;

class Rsa extends Auth
{
    public function index(){

        header('Content-Type:text/html;Charset=utf-8;');
        //证书路径
        $pubfile = ROOT_PATH.'ssl/test.crt';
        $prifile = ROOT_PATH.'ssl/test.pem';
        //apache路径下的openssl.conf文件路径
        $openssl_config_path = "C:/xampp/apache/conf/openssl.cnf";

        $rsa =new \mikkle\tp_tools\Rsa($pubfile,$prifile,$openssl_config_path);
       if($this->request->isPost()){
           $string = isset($_GET['a']) ? $_GET['a'] : '测试123';
           $data = $this->request->post();

//           echo "<pre>";
//           //生成签名
//           echo "\n签名的字符串:\n$string\n\n";
//           $sign = $rsa->sign($string);
//           echo "\n生成签名的值:\n$sign";
//
//           //验证签名
//           $p=$rsa->verify($string, $sign);
//           echo "\n验证签名的值:\n$p";
//
//
//           //加密
//           echo "\n\r加密的字符串:\n$string\n\n";
//           $x = $rsa->encrypt($string);
//           echo "\n生成加密的值:\n$x";
//
//           //解密
           echo "\n未解密的值:\n{$data['password']}";
           $y = $rsa->decrypt($data['password']);
           echo "\n解密的值:\n$y";
           echo "</pre>";

           //创建新的密匙
//        echo "\n创建新的密匙:\n";
//        $rsa->buildNewKey();
       }
        Config::set(['default_return_type'    => 'html',]);

        $sign = $rsa->signByMd5('nietai');
        dump($rsa->verifyByMd5('nietai',$sign));
//        echo "\n生成签名的值:\n$sign";
        return $this->view->fetch('',array(
            'sign'=>$sign
        ));

    }
}
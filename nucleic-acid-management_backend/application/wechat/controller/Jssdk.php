<?php
namespace app\wechat\controller;

use think\Db;
use think\Controller;

class Jssdk extends Controller
{
    public function getTicket($url){
        // 获取access_token
        $access_token = get_access_token();
        $ticket = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi'),true)['ticket'];
        // 生成随机字符串
        $noncestr = mt_rand(100000,999999);
        // 生成时间戳
        $timestamp = time();
        // 生成签名
        $signature = sha1('jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url);
        // 返回数据
        return json(['code'=>200,'msg'=>'获取成功','data'=>['appId'=>config('appid'),'timestamp'=>$timestamp,'nonceStr'=>$noncestr,'signature'=>$signature, 'url'=>$url]]);
    }
}
<?php
namespace app\wechat\controller;

use think\Db;
use think\Controller;

class Mp extends Controller
{
    protected function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        
        $token = 'mayx';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    public function index(){
        if ($this->checkSignature()) {
            if (isset($_GET['echostr'])) {
                echo $_GET['echostr'];
            }else{
                $postStr = file_get_contents("php://input");
                if (!empty($postStr)){
                    $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                    $fromUsername = $postObj->FromUserName;
                    $toUsername = $postObj->ToUserName;
                    $event = $postObj->Event;
                    $time = time();
                    $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
                    if (cache('user_'.$fromUsername)) {
                        $user = cache('user_'.$fromUsername);
                    }else{
                        $user = Db::query("select * from user where wechat_openid=? limit 1",[$fromUsername]);
                        if ($user) {
                            cache('user_'.$fromUsername,$user,3600);
                        }
                    }

                    if($event == 'subscribe'){
                        $msgType = "text";
                        $contentStr = "欢迎使用核酸检测服务！";
                        if ($user) {
                            $contentStr .= "\n欢迎回来！".$user[0]['name'];
                        }else{
                            $contentStr .= "\n请先绑定账号！" . "\n\n<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/wechatfront/bind?openid=" . $fromUsername . "\">点击绑定</a>";
                        }
                    }else if($event == 'CLICK'){
                        $msgType = "text";
                        if ($user) {
                            $contentStr = "你好，". $user[0]['name'] . "\n\n";
                            $contentStr .= "当前可以进行以下操作：\n";

                            $contentStr .= "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/wechatfront/show.html?openid=" . $fromUsername . "\">⚙️展示二维码</a>\n";
                            $contentStr .= "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/wechatfront/read.html?openid=" . $fromUsername . "\">🧪查看近七次检查结果</a>\n";
                            if ($user[0]['gid'] > 1) {
                                $contentStr .= "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/wechatfront/add.html?openid=" . $fromUsername . "\">⭕️执行核酸检测</a>\n";
                            }
                        }else{
                            $contentStr = "请先绑定账号！" . "\n\n<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/wechatfront/bind.html?openid=" . $fromUsername . "\">点击绑定</a>";
                        }
                    }
                    else{
                        echo "";
                        exit;
                    }
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }else {
                    echo "";
                    exit;
                }
            }
        }
    }
    public function init(){
        echo file_get_contents("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" .get_access_token() ,false,stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => '{
                    "button":[
                        {
                            "type":"click",
                            "name":"获取菜单",
                            "key":"MENU"
                        },
                        {
                            "type":"view",
                            "name":"查看公告",
                            "url":"http://'.$_SERVER['HTTP_HOST'].'/wechatfront/notice.html"
                        }
                    ]
                }'
            )
        )));
    }
}
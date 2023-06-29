<?php
namespace app\wechat\controller;

use think\Db;
use think\Controller;

class Notice extends Controller
{
    public function read($id = null){
        if ($id) {
            return json(['code'=>200,'msg'=>'获取成功','data'=>Db::query("select title, content, media_url, create_time from notice where is_del='0' and id=?",[$id])]);
        }
        return json(['code'=>200,'msg'=>'获取成功','data'=>Db::query("select id, title, media_url, create_time from notice where is_del='0' order by create_time desc")]);
    }
}
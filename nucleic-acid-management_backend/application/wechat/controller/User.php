<?php
namespace app\wechat\controller;

use think\Db;
use think\Controller;

class User extends Controller
{
    public function login()
    {
        if (input('post.openid')) {
            $result = Db::query("select id, `name`, gid, uniqid from `user` where is_del='0' and wechat_openid=?",[input('post.openid')]);
            if ($result) {
                session('user', $result[0]);
                return json(['code'=>200,'msg'=>'登录成功','data'=>$result[0]]);
            } else {
                return json(['code'=>400,'msg'=>'登录失败']);
            }
        } else {
            return json(['code'=>400,'msg'=>'登录失败']);
        }
    }
    public function register()
    {
        if (input('post.openid')) {
            if (Db::query("select id from `user` where wechat_openid=?",[input('post.openid')])) {
                return json(['code'=>400,'msg'=>'该账号已注册，请直接登录']);
            } else {
                if (Db::query("select id from `user` where uniqid=? and wechat_openid is null",[input('post.uniqid')])) {
                    Db::execute("update `user` set wechat_openid=? where uniqid=?",[input('post.openid'),input('post.uniqid')]);
                    return json(['code'=>200,'msg'=>'绑定成功']);
                } else if (Db::query("select id from `user` where uniqid=? and wechat_openid is not null",[input('post.uniqid')])) {
                    return json(['code'=>400,'msg'=>'此身份证号/工号已绑定其他微信账号']);
                } else {
                    if (Db::execute("insert into `user` (`name`, uniqid, wechat_openid) values (?,?,?)",[input('post.name'),input('post.uniqid'),input('post.openid')])) {
                        return json(['code'=>200,'msg'=>'注册成功']);
                    } else {
                        return json(['code'=>400,'msg'=>'注册失败']);
                    }
                }
            }
        } else {
            return json(['code'=>400,'msg'=>'注册失败']);
        }
    }
}
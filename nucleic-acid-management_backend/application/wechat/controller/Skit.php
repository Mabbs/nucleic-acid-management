<?php
namespace app\wechat\controller;

use think\Db;
use think\Controller;

class Skit extends Controller
{
    protected $beforeActionList = [
        'auth'
    ];
    protected function auth(){
        if (!session('user')) {
            die(json_encode(['code'=>400,'msg'=>'请先登录']));
        }
    }
    public function add()
    {
        if (session('user')['gid'] < 2) {
            die(json_encode(['code'=>400,'msg'=>'权限不足']));
        }
        $data = input('post.');
        $kid = Db::query("select id from kit where serial=? and is_del='0'", [$data["serial"]]);
        if (!$kid) {
            return json(['code'=>400,'msg'=>'该序列号不存在']);
        }
        try{
            Db::transaction(function () use ($data, $kid) {
                foreach ($data["users"] as $value) {
                    Db::execute("insert into user_kit (kid, uid) values (?,?)", [$kid[0]["id"], $value]);
                }
            });
            return json(['code'=>200,'msg'=>'添加成功']);
        }catch(\Exception $e){
            return json(['code'=>400,'msg'=>'添加失败','data'=>$e->getMessage()]);
        }
    }
    public function read(){
        return json(['code'=>200,'msg'=>'获取成功','data'=>Db::query("SELECT
        user.`name`,
        kit.`status`,
        user_kit.add_time 
    FROM
        user_kit
        INNER JOIN kit ON user_kit.kid = kit.id
        INNER JOIN `user` ON kit.fuid = user.id 
    WHERE
        user_kit.uid = ?
    ORDER BY
        user_kit.add_time DESC 
        LIMIT 7", [session('user')['id']])]);
    }
    public function status(){
        $result = Db::query("SELECT
        kit.`status`
    FROM
        user_kit
        INNER JOIN kit ON user_kit.kid = kit.id
    WHERE
        user_kit.uid = ?
        AND DATE_SUB( CURDATE(), INTERVAL 7 DAY ) <= user_kit.add_time
        AND kit.`status` <> '0' 
    ORDER BY
        user_kit.add_time DESC 
        LIMIT 1", [session('user')['id']]);
        if ($result) {
            if ($result[0]['status'] == '1') {
                return json(['code'=>200,'msg'=>'绿码','data'=>'1']);
            } else {
                return json(['code'=>200,'msg'=>'红码','data'=>'2']);
            }
        } else {
            return json(['code'=>200,'msg'=>'黄码','data'=>'0']);
        }
    }
}
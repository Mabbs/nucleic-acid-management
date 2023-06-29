<?php
namespace app\manage\controller;
use think\Db;
use think\Controller;

class Status extends Controller
{
    protected $beforeActionList = [
        'auth'
    ];
    protected function auth(){
        if (!session('user')) {
            die(json_encode(['code'=>400,'msg'=>'请先登录']));
        }
        if (session('user')['gid'] < 3) {
            die(json_encode(['code'=>400,'msg'=>'权限不足']));
        }
    }
    public function read(){
        return json(['code'=>200,'msg'=>'获取成功','data'=> [
            "WechatUserCount"=> Db::query("select count(*) from user where is_del='0' and gid < 3")[0]["count(*)"], 
            "KitCount"=> Db::query("select count(*) from kit where is_del='0'")[0]["count(*)"],
            "TodayRegisterCount"=> Db::query("select count(*) from user where is_del='0' and gid < 3 and date(create_time)=date(now())")[0]["count(*)"],
            "CheckedRatio"=> Db::query("select count(*) from kit where is_del='0' and status='0'")[0]["count(*)"] / Db::query("select count(*) from kit where is_del='0'")[0]["count(*)"],
            "HistoryRegisterCount"=> Db::query("select count(*) usercount, date(create_time) time from user where is_del='0' and gid < 3 group by date(create_time) limit 7")
        ]]);
    }
}
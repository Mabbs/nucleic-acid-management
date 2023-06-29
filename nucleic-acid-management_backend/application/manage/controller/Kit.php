<?php
namespace app\manage\controller;

use think\Db;
use think\Controller;

class Kit extends Controller
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
    public function add(){
        try{
            Db::execute("insert into kit (serial, fuid) values (?,?)",[input('post.serial'),session("user")['id']]);
            return json(['code'=>200,'msg'=>'添加成功']);
        }catch(\Exception $e){
            return json(['code'=>400,'msg'=>'添加失败','data'=>$e->getMessage()]);
        }
    }
    public function read($offset = 0, $limit = 10, $serial = '', $status = ''){
        $sqlparam = [];
        $sql = "select SQL_CALC_FOUND_ROWS kit.id, kit.serial, kit.status, user.name, kit.create_time, kit.update_time from kit inner join user on kit.fuid=user.id where kit.is_del='0'";
        if (session("user")['gid'] == 3) {
            $sql .= " and fuid=?";
            array_push($sqlparam, session("user")['id']);
        }
        if ($serial) {
            $sql .= " and serial like ?";
            array_push($sqlparam, "%$serial%");
        }
        if ($status != '') {
            $sql .= " and status=?";
            array_push($sqlparam, $status);
        }
        $sql .= " limit ?,?";
        array_push($sqlparam, $offset, $limit);
        return json(['code'=>200,'msg'=>'获取成功','data'=>Db::query($sql,$sqlparam),'count'=>Db::query("select FOUND_ROWS() as count")[0]['count']]);
    }
    public function update($serial){
        /*
        核酸结果通知：
        尊敬的用户你好，
        你在{{time.DATA}}时所做的核酸结果为：
        {{result.DATA}}
        */
        $template_id = '';
        if (session("user")['gid'] == 4) {
            $kit = Db::query("select id from kit where serial=? and is_del='0'",[$serial]);
        } else {
            $kit = Db::query("select id from kit where serial=? and is_del='0' and fuid=?",[$serial,session("user")['id']]);
        }
        if ($kit) {
            try{
                if (Db::execute("update kit set status=? where id=?", [input('post.status'),$kit[0]['id']])) {
                    if (!function_exists('fastcgi_finish_request')) {
                        return json(['code'=>200,'msg'=>'修改成功']);
                    }
                    header('Content-Type:application/json;charset=utf-8');
                    echo json_encode(['code'=>200,'msg'=>'修改成功']);
                    fastcgi_finish_request();
                    session_write_close();
                    if (input('post.status') != 0) {
                        foreach (Db::query("SELECT
                        user_kit.add_time,
                        `user`.wechat_openid 
                    FROM
                        `user`
                        INNER JOIN user_kit ON `user`.id = user_kit.uid 
                    WHERE
                        user_kit.kid = ? 
                        AND `user`.wechat_openid IS NOT NULL",[$kit[0]['id']]) as $value) {
                            file_get_contents('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.get_access_token(), false, stream_context_create(array('http' => array('method'=>'POST','header'=>"Content-Type: application/json;charset=utf-8",'content'=>'{
                                "touser":"'.$value['wechat_openid'].'",
                                "template_id":"'.$template_id.'",
                                "data":{
                                    "time":{
                                        "value":"'.$value['add_time'].'"
                                    },
                                    "result":{
                                        "value":"'.(input('post.status') == 1 ? '阴性' : '阳性').'",
                                        "color":"'.(input('post.status') == 1 ? '#00FF00' : '#FF0000').'"
                                    }
                                }
                            }'))));
                        }
                    }
                    return;
                } else {
                    return json(['code'=>400,'msg'=>'修改失败']);
                }
            }catch(\Exception $e){
                return json(['code'=>400,'msg'=>'修改失败','data'=>$e->getMessage()]);
            }
        } else {
            return json(['code'=>400,'msg'=>'该对象不存在']);
        }
    }
    public function delete($id){
        if (Db::execute("update kit set is_del='1' where id=?",[$id])) {
            return json(['code'=>200,'msg'=>'删除成功']);
        } else {
            return json(['code'=>400,'msg'=>'删除失败']);
        }
    }
}
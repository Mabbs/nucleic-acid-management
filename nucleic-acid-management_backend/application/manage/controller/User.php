<?php
namespace app\manage\controller;
use think\Db;
use think\Controller;

class User extends Controller
{
    protected $beforeActionList = [
        'auth' => ['except'=>'login,register']
    ];
    protected function auth(){
        if (!session('user')) {
            die(json_encode(['code'=>400,'msg'=>'请先登录']));
        }
        if (session('user')['gid'] != 4) {
            die(json_encode(['code'=>400,'msg'=>'权限不足']));
        }
    }
    public function login()
    {
        $result = Db::query("select id, name, gid, uniqid from user where is_del='0' and uniqid=? and password=md5(?)",[input('post.uniqid'),input('post.password')]);
        if ($result) {
            session('user', $result[0]);
            return json(['code'=>200,'msg'=>'登录成功','data'=>$result[0]]);
        } else {
            return json(['code'=>400,'msg'=>'登录失败']);
        }
    }
    public function register()
    {
        if (Db::query("select id from user where uniqid=?",[input('post.uniqid')])) {
            return json(['code'=>400,'msg'=>'该账号已申请或注册']);
        } else {
            if (Db::execute("insert into user (name, password, uniqid, gid, is_del) values (?,md5(?),?, 3, '1')",[input('post.name'),input('post.password'),input('post.uniqid')])) {
                return json(['code'=>200,'msg'=>'申请成功']);
            } else {
                return json(['code'=>400,'msg'=>'申请失败']);
            }
        }
    }

    public function create(){
        if (Db::execute("insert into user (name, password, uniqid, gid) values (?,md5(?),?,?)",[input('post.name'),input('post.password'),input('post.uniqid'),input('post.gid')])) {
            return json(['code'=>200,'msg'=>'添加成功']);
        } else {
            return json(['code'=>400,'msg'=>'添加失败']);
        }
    }
    public function read($offset = 0, $limit = 10, $name = '', $gid = ''){
        $sqlparam = [];
        $sql = "select SQL_CALC_FOUND_ROWS id, name, uniqid, gid, create_time, update_time from user where is_del='0'";
        if ($name) {
            $sql .= " and name like ?";
            array_push($sqlparam, "%$name%");
        }
        if ($gid) {
            $sql .= " and gid=?";
            array_push($sqlparam, $gid);
        }
        $sql .= " limit ?,?";
        array_push($sqlparam, $offset, $limit);
        return json(['code'=>200,'msg'=>'获取成功','data'=>Db::query($sql,$sqlparam), 'count'=>Db::query("select FOUND_ROWS() as total")[0]['total']]);
    }
    public function readPending($offset = 0, $limit = 10, $name = ''){
        $sqlparam = [];
        $sql = "select SQL_CALC_FOUND_ROWS id, name, uniqid, gid, create_time, update_time from user where is_del='1' and gid='3'";
        if ($name) {
            $sql .= " and name like ?";
            array_push($sqlparam, "%$name%");
        }
        $sql .= " limit ?,?";
        array_push($sqlparam, $offset, $limit);
        return json(['code'=>200,'msg'=>'获取成功','data'=>Db::query($sql,$sqlparam), 'count'=>Db::query("select FOUND_ROWS() as total")[0]['total']]);
    }
    public function passPending($id){
        if (Db::execute("update user set is_del='0' where id=?",[$id])) {
            return json(['code'=>200,'msg'=>'审核成功']);
        } else {
            return json(['code'=>400,'msg'=>'审核失败']);
        }
    }
    public function update($id){
        if (Db::execute("select id from user where is_del='0' and id=?",[$id])) {
            if (input('post.password')) {
                if (Db::execute("update user set name=?, password=md5(?), uniqid=?, gid=? where id=?", [input('post.name'),input('post.password'),input('post.uniqid'),input('post.gid'),$id])) {
                    return json(['code'=>200,'msg'=>'修改成功']);
                } else {
                    return json(['code'=>400,'msg'=>'修改失败']);
                }
            } else {
                if (Db::execute("update user set name=?, uniqid=?, gid=? where id=?", [input('post.name'),input('post.uniqid'),input('post.gid'),$id])) {
                    return json(['code'=>200,'msg'=>'修改成功']);
                } else {
                    return json(['code'=>400,'msg'=>'修改失败']);
                }
            }
        } else {
            return json(['code'=>400,'msg'=>'用户不存在']);
        }
    }
    public function delete($id){
        if (Db::execute("update user set is_del='1' where id=?",[$id])) {
            return json(['code'=>200,'msg'=>'删除成功']);
        } else {
            return json(['code'=>400,'msg'=>'删除失败']);
        }
    }
}
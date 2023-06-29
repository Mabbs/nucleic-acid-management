<?php
namespace app\manage\controller;

use think\Db;
use think\Controller;
use think\facade\Env;

class Notice extends Controller
{
    protected $beforeActionList = [
        'auth'
    ];
    protected function auth(){
        if (!session('user')) {
            die(json_encode(['code'=>400,'msg'=>'请先登录']));
        }
        if (session('user')['gid'] != 4) {
            die(json_encode(['code'=>400,'msg'=>'权限不足']));
        }
    }
    public function create(){
        if (Db::execute("insert into notice (title, content, media_url, uid) values (?,?,?,?)",[input('post.title'),input('post.content'),input('post.media_url'),session('user')['id']])) {
            return json(['code'=>200,'msg'=>'添加成功']);
        } else {
            return json(['code'=>400,'msg'=>'添加失败']);
        }
    }
    public function read($offset = 0, $limit = 10){
        return json(['code'=>200,'msg'=>'获取成功','data'=>Db::query("select SQL_CALC_FOUND_ROWS id, title, content, media_url, create_time, update_time from notice where is_del='0' limit ?,?",[$offset,$limit]),'count'=>Db::query("select FOUND_ROWS() as count")[0]['count']]);
    }
    public function update($id){
        if (Db::execute("select id from notice where is_del='0' and id=?",[$id])) {
            if (Db::execute("update notice set title=?, content=?, media_url=? where id=?", [input('post.title'),input('post.content'),input('post.media_url'),$id])) {
                return json(['code'=>200,'msg'=>'修改成功']);
            } else {
                return json(['code'=>400,'msg'=>'修改失败']);
            }
        } else {
            return json(['code'=>400,'msg'=>'该公告不存在']);
        }
    }
    public function delete($id){
        if (Db::execute("select id from notice where is_del='0' and id=?",[$id])) {
            if (Db::execute("update notice set is_del='1' where id=?",[$id])) {
                return json(['code'=>200,'msg'=>'删除成功']);
            } else {
                return json(['code'=>400,'msg'=>'删除失败']);
            }
        } else {
            return json(['code'=>400,'msg'=>'该公告不存在']);
        }
    }
    public function upload(){
        $file = request()->file('file');
        if (!$file->checkExt('jpg,png,gif,jpeg,bmp')) {
            return json(['code'=>400,'msg'=>'上传失败，文件类型错误']);
        }
        $info = $file->move(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . 'uploads');
        if ($info) {
            return json(['code'=>200,'msg'=>'上传成功','data'=>['media_url'=>'/uploads/'.$info->getSaveName()]]);
        } else {
            return json(['code'=>400,'msg'=>'上传失败']);
        }
    }
}
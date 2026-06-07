<?php
namespace app\common\controller;

use app\common\basics\Auth;
use app\common\model\EditorModel;
use app\common\model\UploadModel;
use app\common\model\UserModel;
use app\common\service\LoginService;
use think\App;


class Account extends Auth
{
    protected $notNeedPower = ['info','logout','password','notice','uploadinfo','upload'];
    public function info(){
        $userinfo=array();
        $userinfo['uid']=$this->userItem['id'];
        $userinfo['username']=$this->userItem['username'];
        $userinfo['avatar']='https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif?imageView2/1/w/80/h/80';
        $userinfo['roles'] = [$this->userItem['type']];
        $userinfo['order_status'] = $this->userItem['order_status'];
        $userinfo['balance'] = $this->userItem['balance'];
        return $this->success("SUCCESS",$userinfo);
    }

    public function logout(){
        $token = request()->header('Api-Token');
        LoginService::logout($token);
        return $this->success("退出成功");
    }

    /**
     * @api {GET} /super/user/password 修改密码
     * @apiVersion 1.0.0
     * @apiName password
     * @apiGroup user
     *
     * @apiParam {String} password_old 旧密码
     * @apiParam {String} password_new 新密码
     * @apiParam {String} google_key 谷歌验证码
     *
     * @apiHeader {String} token TOKEN
     *
     * @apiSuccess {Int} code 状态码
     * @apiSuccess {String} msg 提示内容
     * @apiSuccess {String} data 返回数据
     * @apiSuccessExample  {json} success-example
    {
    "code": 1,
    "msg": "修改成功"
    }
     */
    public function password(){
        $password_old=$this->request->param('password_old');
        $password_new=$this->request->param('password_new');

        if(empty($password_old)){
            return $this->error("请输入原密码");
        }
        if(strlen($password_new) < 6){
            return $this->error("新密码长度不可小于6位");
        }
        if($password_old == $password_new){
            return $this->error("原密码与新密码不可相同");
        }

        $user_model=new UserModel();

        if(get_password($password_old,$this->userItem['rand_code']) != $this->userItem['password']){
            return $this->error("原密码不正确");
        }

        $data=[];
        $data['rand_code'] = get_rand_str(4);
        $data['password'] = get_password($password_new,$data['rand_code']);
        $req=$user_model->where('id','=',$this->userItem['id'])->update($data);
        if($req){
            return $this->success("修改成功");
        }else{
            return $this->error("修改失败");
        }
    }

    public function notice(){

        $notice=new EditorModel();
        $data=$notice->getContentByKey('notice');
        return $this->success("获取成功",$data);
    }

    //获取上传地址
    public function uploadinfo(){
        $url=url('upload',[],'',true)->build();
        $data=[];
        $data['action'] = $url;
        $data['headers'] = array(
            'Api-Token'=>$this->request->header('Api-Token')
        );
        return $this->success("获取成功",$data);
    }

    //上传图片
    public function upload(){
        try {
            validate(
                [
                'image'=>['fileSize'=>1024*1024*4,'fileExt'=>'jpg,png,gif']
                ]
            )->check(request()->file());

            $image = request()->file('image');
            if($image){
                $savename = \think\facade\Filesystem::disk('public')->putFile( 'topic', $image);
                $savename = \think\facade\Filesystem::getDiskConfig('public','url').'/'.str_replace("\\","/",$savename);

                $data = [];
                $data['name'] = $image->getFilename();
                $data['size'] = $image->getSize();
                $data['mime'] = $image->getMime();
                $data['type'] = 'image';
                $data['time'] = time();
                $data['path'] = $savename;
                $uploadModel = new UploadModel();
                $uploadModel->insert($data);

                return $this->success("上传成功",request()->domain().$savename);
            }
        } catch (\think\exception\ValidateException $e) {
            return $this->error($e->getMessage());
        }
    }
}

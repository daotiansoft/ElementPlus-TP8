<?php
namespace app\common\controller;

use app\common\basics\Auth;
use app\common\model\EditorModel;
use app\common\model\UploadModel;
use app\common\model\UserModel;
use app\common\service\LoginService;
use app\common\service\PermsAccessService;
use app\common\utils\AjaxUtils;
use think\App;


class Account extends Auth
{
    protected $notNeedPower = ['info','logout','password','notice','uploadinfo','upload','getRoutes'];
    public function info(){
        $userinfo=array();
        $userinfo['uid']=$this->userItem['id'];
        $userinfo['username']=$this->userItem['username'];
        $userinfo['nickname']=$this->userItem['nickname'];
        $userinfo['avatar']=$this->userItem['avatar'];
        $userinfo['roles'] = [$this->userItem['type']];

        if(empty($userinfo['nickname'])){
            $userinfo['nickname'] = $userinfo['username'];
        }
        // 头像为空时由前端使用默认占位，不依赖第三方图床
        return AjaxUtils::success("SUCCESS",$userinfo);
    }

    public function logout(){
        $token = request()->header('Api-Token');
        LoginService::logout($token);
        return AjaxUtils::success("退出成功");
    }

    /**
     * @api {GET} /common/account/password 修改密码
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
            return AjaxUtils::error("请输入原密码");
        }
        if(strlen($password_new) < 6){
            return AjaxUtils::error("新密码长度不可小于6位");
        }
        if($password_old == $password_new){
            return AjaxUtils::error("原密码与新密码不可相同");
        }

        $user_model=new UserModel();

        if(!verify_password($password_old, $this->userItem['password'])){
            return AjaxUtils::error("原密码不正确");
        }

        $data=[];
        $data['password'] = make_password($password_new);
        $req=$user_model->where('id','=',$this->userItem['id'])->update($data);
        if($req){
            // 密码已变更，立即作废此前签发的 token
            LoginService::invalidateUserTokens((int) $this->userItem['id']);
            return AjaxUtils::success("修改成功，请重新登录");
        }else{
            return AjaxUtils::error("修改失败");
        }
    }

    public function notice(){

        $notice=new EditorModel();
        $data=$notice->getContentByKey('notice');
        return AjaxUtils::success("获取成功",['content'=>$data]);
    }

    //获取上传地址
    public function uploadinfo(){
        $url=url('upload',[],'',true)->build();
        $data=[];
        $data['action'] = $url;
        $data['headers'] = array(
            'Api-Token'=>$this->request->header('Api-Token')
        );
        return AjaxUtils::success("获取成功",$data);
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

                return AjaxUtils::success("上传成功",['url'=>request()->domain().$savename]);
            }
        } catch (\think\exception\ValidateException $e) {
            return AjaxUtils::error($e->getMessage());
        }
    }

    public function getRoutes(){
        $data = PermsAccessService::getRoutes($this->userItem['type']);
        return AjaxUtils::success($this->userItem['type'],$data);
    }
}

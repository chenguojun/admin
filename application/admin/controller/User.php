<?php
namespace app\admin\controller;
use think\Controller;
use think\captcha\Captcha;
use app\common\controller\Common;
use app\common\controller\Base;
use think\Model;

class User extends Base{
    public function user_list(){
        $list = new Common;
        $list->add_Crumb("用户管理",Url())
            ->add_Crumb("用户列表","")
            ->add_field("id","id","id")
            ->add_field("username","用户名","text")
            ->set_edit_url(url("user_edit",[],""),"编辑用户")
            ->set_add_url(url("user_add"),"新建用户")
            ->set_delete_url(url("user_delete"),"删除用户")
            ->table_name("user")
            ->set_map("id")
            ->set_title("用户列表")
            ->set_notsort("[0,3]")
            ->common_list();
    }
    public function user_add(){
        $user = Model("User");
        $role_list = $user->get_role_rule();
        $role_list1 = [];
        foreach ($role_list as $item){
            $role_list1[$item["id"]]=$item["role_name"];
        }
        $list = new Common;
        $list->add_Crumb("用户管理",Url())
            ->add_Crumb("用户列表","")
            ->add_field("username","用户名","text",["validate"=>"require"])
            ->add_field("password","密码","password",["validate"=>"require","function"=>"md5"])
            ->add_field("repassword","确认密码","repassword",["validate"=>"require|confirm:password","function"=>"md5"])
            ->add_field("role","账号角色","select",["options"=>$role_list1])
            ->add_field("hidden","隐藏的","hidden",["value"=>"123"])
            ->table_name("user")
            ->set_title("用户列表")
            ->add();
    }
    public function user_edit(){
        $user = Model("User");
        $role_list = $user->get_role_rule();
        $role_list1 = [];
        foreach ($role_list as $item){
            $role_list1[$item["id"]]=$item["role_name"];
        }
        $list = new Common;
        $list->add_field("username","用户名","text",["validate"=>"require"])
            ->add_field("role","账号角色","select",["options"=>$role_list1])
            ->table_name("user")
            ->set_map("id")
            ->edit();
    }
    public function role(){
        $role_1ist = db("role")->select();
        $user_list = db("user")->select();
        $list=[];
        foreach ($user_list as $i){
            $role_id = $i["role"];
            $list[$role_id][]=$i["username"];
        }
        var_dump($list);
        $this->assign("role_list",$role_1ist);
        $this->assign("user_list",$list);
        return $this->fetch("admin@/role");
    }
    public function role_add(){
        if(request()->isPost()){
            $post = input("post.");
            if (empty($post["role_nam"])){
                $this->error("角色名不能为空");
            }
            $auth = "";
            if(!empty($post["auth"])){
                foreach ($post["auth"] as $item){
                    $auth .= $item ."|";
                }
            }
            $index = db("role")->insert(["role_name"=>$post["role_name"],"description"=>$post["description"],"value"=>$auth]);
            system_log("新增角色(id:".$index.")","角色操作");
            $this->success("新增成功");
            exit();
        }
        return $this->fetch("admin@/role_add");
    }
    public function login(){
        if (request()->isPost()) {
            $username = input('username');
            $password = input('password');
            $captcha = input('captcha');
            if (empty($username)) {
                $this->error('用户名不能为空');
            }
            if (empty($password)) {
                $this->error('密码不能为空');
            }
            if(!captcha_check($captcha)){
                $this->error('验证码错误');
            };
            $User = Model('User');
            if (!$User->is_user_exists($username)) {
                $this->error('用户不存在');
            }
            if (!$User->check_login($username, $password)) {
                $this->error('登录密码错误');
            }
            $auth_token = $User->refresh_auth_token($username, $password);
            if (!$auth_token) {
                $this->error('登录失败，请重试');
            }
            session("auth_token",$auth_token);
            system_log("登录系统成功","登录系统");
            $this->success('登录成功', url("Index/index"));
        }else{
            echo $this->fetch("admin@/login");
        }
    }
    public function get_captcha(){
        $captcha = new Captcha();
        $captcha->codeSet = '0123456789';
        $captcha->fontSize = 50;
        return $captcha->entry();
    }
    public function logout(){
        system_log("退出登录成功","登出系统");
        $User = Model("User");
        $User->logout();
        session("auth_token",null);
        $this->success("退出登录成功",url("User/login"));
    }
    public function change_pwd(){
        if (request()->isPost()){
            $User = Model("User");
            $User->change_password(input("newpassword"));
            system_log("修改密码成功","账户操作");
            $this->success("修改成功");
        }
        return $this->fetch("admin@/change_pwd");
    }
    public function role_delete(){
        if(request()->isPost()){
            $User = Model("User");
            if($User->role_delete(input("id"))){
                system_log("删除角色","角色操作");
                echo json_encode(["status"=>1]);
            }
        }
    }
}
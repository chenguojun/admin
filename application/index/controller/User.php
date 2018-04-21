<?php
namespace app\index\controller;
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
    public function role(){
        $role_1ist = db("role")->select();
        $this->assign("role_list",$role_1ist);
        return $this->fetch("Index@/role");
    }
    public function role_add(){
        if(request()->isPost()){
            $k=1;
            $post = input("post.");
            $auth = "";
            foreach ($this->auth as $item){
                if(empty($item["sub_auth"])){
                    if(!empty($post["auth_".$k])){
                        foreach ($post["auth_".$k] as $key=>$item1){
                            if($item1=="1" && $key!="own"){
                                $auth .= $item["action"][$key]."|";
                            }
                        }
                    }
                }else{
                    $i=1;
                    foreach ($item["sub_auth"] as $item1){
                        foreach ($post["auth_".$k."_".$i] as $key=>$item2){
                            if($item2=="1" && $key!="own"){
                                $auth .= $item1["action"][$key]."|";
                            }
                        }
                        $i++;
                    }
                }
                $k++;
            }
            db("role")->insert(["role_name"=>$post["role_name"],"description"=>$post["description"],"value"=>$auth]);
            $this->success("新增成功");
            exit();
        }
        return $this->fetch("Index@/role_add");
    }
    public function login(){
        if (request()->isPost()) {
            $username = input('username');
            $password = input('password');
            if (empty($username)) {
                $this->error('用户名不能为空');
            }
            if (empty($password)) {
                $this->error('密码不能为空');
            }
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
            $this->success('登录成功', url("Index/index"));
        }else{
            echo $this->fetch("index@/login");
        }
    }
    public function get_captcha(){
        $captcha = new Captcha();
        $captcha->codeSet = '0123456789';
        $captcha->fontSize = 50;
        return $captcha->entry();
    }
    public function logout(){
        $User = Model("User");
        $auth_token = $User->get_user_info()["auth_token"];
        $User->logout();
        session("auth_token","");
        $this->success("退出登录成功",url("User/login"));
    }
    public function change_pwd(){
        if (request()->isPost()){
            $User = Model("User");
            $User->change_password(input("newpassword"));
            $this->success("修改成功");
        }
        return $this->fetch("Index@/change_pwd");
    }
    public function role_delete(){
        if(request()->isPost()){
            $User = Model("User");
            if($User->role_delete(input("id"))){
                echo json_encode(["status"=>1]);
            }
        }
    }
}
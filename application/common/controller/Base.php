<?php
namespace app\common\controller;
use \think\Controller;
class Base extends Controller{
    protected $auth;
    public function ok(){
        echo "<script type='text/javascript'>var index = parent.layer.getFrameIndex(window.name);parent.layer.close(index);</script>";
    }
    public function _initialize(){
        parent::_initialize();
        $user = Model("User");
        if(get_now_action() != "admin.User.login" && get_now_action() != "admin.User.get_captcha"){
            if(!session("auth_token")){
                $this->error("请先登录系统",url("User/login"));
            }
            if(!$user->check_auth_token(session("auth_token"))){
                $this->error("登录信息已过期，请重新登录",url("User/login"));
            }
        }
        $menu = [
            ["title"=>"资讯管理","name"=>"article","icon"=>"&#xe616;","auth"=>true,"sub_menu"=>[
                ["title"=>"资讯管理","url"=>Url("Index/article"),"auth"=>[
                    "add"=>"admin.Index.article_add",
                    "delete"=>"admin.Index.article_delete",
                    "edit"=>"admin.Index.article_edit",
                    "view"=>"admin.Index.article"
                ]]
            ]],
            ["title"=>"图片管理","name"=>"picture","icon"=>"&#xe613;","sub_menu"=>[
                ["title"=>"图片管理","url"=>Url("Index/picture_list")]
            ]],
            ["title"=>"用户管理","name"=>"user","icon"=>"&#xe62c;","auth"=>true,"sub_menu"=>[
                ["title"=>"用户管理","url"=>Url("User/user_list")],
                ["title"=>"角色管理","url"=>Url("User/role"),"auth"=>[
                    "add"=>"admin.Index.role_add",
                    "delete"=>"admin.Index.role_delete",
                    "view"=>"admin.User.role"
                ]]
            ]],
            ["title"=>"系统管理","name"=>"system","icon"=>"&#xe61d;","auth"=>true,"sub_menu"=>[
                ["title"=>"系统日志","url"=>Url("System/log"),"auth"=>[
                    "add"=>"admin.Index.role_add",
                    "delete"=>"admin.Index.role_delete",
                    "view"=>"admin.System.log"
                ]],
            ]],
        ];
        $this->assign("menu",$menu);
        $auth = "";
        foreach ($menu as $item){
            foreach ($item["sub_menu"] as $k){
                if (!empty($k["auth"])){
                    foreach ($k["auth"] as $key=>$value){
                        $auth .= $value."|";
                    }
                }
            }
        }
        if(strpos($auth,get_now_action())){  //如果需要权限认证
            if(!$user->check_auth(get_now_action(),session("auth_token"))){
                $this->error("很抱歉，你没有权利访问");
            }

        }
    }
}
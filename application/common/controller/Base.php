<?php
namespace app\common\controller;
use \think\Controller;
class Base extends Controller{
    protected $auth;
    public function _initialize(){
        parent::_initialize();
        $menu = [
            ["title"=>"资讯管理","name"=>"article","icon"=>"&#xe616;","sub_menu"=>[
                ["title"=>"资讯管理","url"=>Url("Index/article")]
            ]],
            ["title"=>"图片管理","name"=>"picture","icon"=>"&#xe613;","sub_menu"=>[
                ["title"=>"图片管理","url"=>Url("Index/picture_list")]
            ]],
            ["title"=>"用户管理","name"=>"user","icon"=>"&#xe613;","sub_menu"=>[
                ["title"=>"用户管理","url"=>Url("User/user_list")],
                ["title"=>"角色管理","url"=>Url("User/role")]
            ]],
        ];
        $this->assign("menu",$menu);
        $this->auth = [
            ["name_c"=>"咨询管理","action"=>
                [
                    "add"=>"index.Index.article_add",
                    "delete"=>"index.Index.article_delete",
                    "edit"=>"index.Index.article_edit",
                    "view"=>"index.Index.article"
                ]
            ],
            ["name_c"=>"图片管理","sub_auth"=>
                [
                    ["name_c"=>"图片1管理","action"=>
                        [
                            "add"=>"index.Index.picture1_add",
                            "delete"=>"index.Index.picture1_delete",
                            "edit"=>"index.Index.picture1_edit",
                            "view"=>"index.Index.picture1"
                        ]
                    ],
                    ["name_c"=>"图片2管理","action"=>
                        [
                            "add"=>"index.Index.picture2_add",
                            "delete"=>"index.Index.picture2_delete",
                            "edit"=>"index.Index.picture2_edit",
                            "view"=>"index.Index.picture2"
                        ]
                    ]
                ]
            ],
        ];
        $this->assign("auth",$this->auth);
        $auth = "";
        foreach ($this->auth as $item){
            if(empty($item["sub_auth"])){
                foreach ($item["action"] as $key=>$item1){
                    $auth .= $item["action"][$key]."|";
                }
            }else{
                foreach ($item["sub_auth"] as $item1){
                    foreach ($item1["action"] as $key=>$item2){
                        $auth .= $item1["action"][$key]."|";
                    }
                }
            }
        }
        $now = request()->module().".".request()->controller().".".request()->action()."|";
        if(strpos($auth,$now)){  //如果需要权限认证
            if(!session("auth_token")){
                $this->error("请先登录系统",url("User/login"));
            }
            $user = Model("User");
            if(!$user->check_auth_token(session("auth_token"))){
                $this->error("登录信息已过期，请重新登录",url("User/login"));
            }
            if(!$user->check_auth($now,session("auth_token"))){
                $this->error("很抱歉，你没有权利访问");
            }

        }
    }
}
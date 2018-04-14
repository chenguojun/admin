<?php
namespace app\index\controller;
use app\common\controller\Common;
use think\Controller;
class Index extends Controller{
    public function _initialize(){
        parent::_initialize();
        $menu = [
            ["title"=>"资讯管理","name"=>"article","icon"=>"&#xe616;","sub_menu"=>[
                ["title"=>"资讯管理","url"=>Url("article")]
            ]],
            ["title"=>"图片管理","name"=>"picture","icon"=>"&#xe613;","sub_menu"=>[
                ["title"=>"图片管理","url"=>Url("picture_list")]
            ]],
        ];
        $this->assign("menu",$menu);
    }

    public function index(){
        echo $this->fetch("index@/index");
    }
    public function article_add(){
        $list = new Common;
        $list->add_field("img","图片","image")
            ->add_field("title","名字","text",["placeholder"=>"测试place","value"=>"123","require"=>true,"validate"=>"require|max:25"])
            ->add_field("fenlei","分类栏目","select",["options"=>["1"=>"选择1","2"=>["选择2","selected"]],"require"=>true])
            ->add_field("zhaiyao","文章摘要","textarea",["placeholder"=>"说点什么...最少输入10个字符","datatype"=>"*10-100","max"=>200,"require"=>true,"validate"=>"require|max:1000"])
            ->add_field("pinglun","允许评论","checkbox",["require"=>true])
            ->add_field("detail","详情","editor",["require"=>true,"validate"=>"require|max:1000"])
            ->add_field("selltime","销售时间","date",["require"=>true,"validate"=>"require"])
            ->add_field("fenlei1","分类栏目1","radio",["options"=>["1"=>"选择1","2"=>["选择2","selected"]],"require"=>true])
            ->add_field("images","图片","images")
            ->table_name("article")
            ->add();
    }
    public function article_edit(){
        $list = new Common;
        $list->add_field("title","名字","text",["placeholder"=>"测试place","require"=>true,"validate"=>"require|max:25"])
            ->add_field("fenlei","分类栏目","select",["options"=>["1"=>"选择1","2"=>"选择2"],"require"=>true])
            ->add_field("zhaiyao","文章摘要","textarea",["placeholder"=>"说点什么...最少输入10个字符","datatype"=>"*10-100","max"=>200,"require"=>true,"validate"=>"require|max:1000"])
            ->add_field("pinglun","允许评论","checkbox",["require"=>true])
            ->add_field("detail","详情","editor",["require"=>true,"validate"=>"require|max:1000"])
            ->add_field("selltime","销售时间","date",["require"=>true,"validate"=>"require"])
            ->add_field("fenlei1","分类栏目1","radio",["options"=>["1"=>"选择1","2"=>"选择2"],"require"=>true])
            ->add_field("img","图片2","image")
            ->add_field("images","图片1","images")
            ->table_name("article")
            ->set_map("id")
            ->edit();
    }
    public function article(){
        $list = new Common;
        $list->add_Crumb("资讯管理",Url())
            ->add_Crumb("资讯列表","")
            ->add_field("id","id","id")
            ->add_field("img","图片","image",["attr"=>"width=50 height=50"])
            ->add_field("title","名字","text",["width"=>"10%"])
            ->add_field("fenlei","分类栏目","select",["options"=>["1"=>"选择1","2"=>"选择2","4"=>"选择4"]])
            ->add_field("pinglun","允许评论","checkbox")
            ->add_field("selltime","销售时间","date")
            ->add_field("fenlei1","分类栏目1","radio",["options"=>["1"=>"选择1","2"=>"选择2"],"sort"=>true])
            ->set_edit_url(url("article_edit",[],""),"编辑资讯")
            ->set_add_url(url("article_add"),"增加资讯")
            ->table_name("article")
            ->set_map("id")
            ->set_title("文章列表")
            ->common_list();
    }
}


<?php
namespace app\admin\controller;
use app\common\controller\Common;
use app\common\controller\Base;
class Index extends Base{
    public function index(){
        echo $this->fetch("admin@/index");
    }
    public function article(){
        $list = new Common;
        $list->add_Crumb("资讯管理",Url())
            ->add_Crumb("资讯列表","")
            ->add_field("id","id","id")
            ->add_field("img","图片","image",["attr"=>"width=50 height=50"])
            ->add_field("title","名字","text",["width"=>"10%","placeholder"=>"测试place","value"=>"123","require"=>true,"validate"=>"require|max:25"])
            ->add_field("fenlei","分类栏目","select",["options"=>["1"=>"选择1","2"=>["选择2","selected"],"require"=>true]])
            ->add_field("pinglun","允许评论","checkbox",["require"=>true])
            ->add_field("detail","详情","editor",["require"=>true,"validate"=>"require|max:1000"],["add","edit"])
            ->add_field("selltime","销售时间","date",["require"=>true,"validate"=>"require"])
            ->add_field("fenlei1","分类栏目1","radio",["options"=>["1"=>"选择1","2"=>["选择2","selected"]],"sort"=>true])
            ->add_field("time","时间","function",["function_name"=>"date_now","parameter"=>["##"]],["view"])
            ->add_field("images","图片","images",null,["add","edit"])
            ->set_name("资讯")
            ->set_search("fenlei","selltime","title")
            ->table_name("article")
            ->set_map("id")
            ->set_notsort("[0,2,9]")
            ->common();
    }
}


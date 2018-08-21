<?php
namespace app\admin\controller;
use app\common\controller\Base;
use app\common\controller\Common;
class System extends Base{
    public function log(){
        $list = new Common;
        $list->add_Crumb("系统管理",Url())
            ->add_Crumb("系统日志","")
            ->add_field("id","id","id")
            ->add_field("type","类型","text")
            ->add_field("content","内容","text",["width"=>"30%"])
            ->add_field("username","用户名","text")
            ->add_field("ip","客户端ip","text")
            ->add_field("time","时间","text")
            ->table_name("system_log")
            ->set_map("id")
            ->set_name("日志")
            ->set_buttons(false,false,false)
            ->set_notsort("[0,3]")
            ->common();
    }
}
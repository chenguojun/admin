<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $filename = 'install.lock';
        if (!file_exists($filename)) {
            $this->redirect("system/install/index");
        }
    }
    public function index(){
        echo "这里是主页,<a href='". url("admin/User/login") ."'>进入后台</a>";
    }
}
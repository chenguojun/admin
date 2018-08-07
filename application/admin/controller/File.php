<?php
namespace app\admin\controller;
use think\Validate;
use think\Request;

class File{
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>15678,'ext'=>'jpg,png,gif,jpge,bmp'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            echo json_encode(["status"=>1,"savename"=> '/public/uploads/' .date("Ymd")."/".$info->getFilename(),"filename"=>$info->getFilename()]);
        }else{
            echo json_encode(["status"=>0,"reason"=>$file->getError()]);
        }
    }
}
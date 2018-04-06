<?php
namespace app\common\model;

use think\Model;

class Common extends Model{
    public $table = '';
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
    public function get_true_data(){
        $data_raw = $this->select();
        $data = ["data_list"=>[],"count"=>0];
        foreach ($data_raw as $item){
            array_push($data["data_list"],$item->data);
        }
        $data["count"] = $this->count();
        return $data;
    }
}
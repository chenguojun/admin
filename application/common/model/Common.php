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
    public function get_true_data($search_map=null,$search_data=null){
        if ($search_data == null){
            $data_raw = $this->select();
        }else{
            $data_raw = $this;
            if ($search_data["search_kind"]!=0){
                $data_raw = $data_raw->where($search_map["kind"],$search_data["search_kind"]);
            }
            if($search_data["search_name"]!=""){
                $data_raw = $data_raw->where($search_map["name"],"LIKE","%".$search_data["search_name"]."%");
            }
            if($search_data["search_time_min"]!="" && $search_data["search_time_max"]!=""){
                $data_raw = $data_raw->where($search_map["time"],"between time",[$search_data["search_time_min"],$search_data["search_time_max"]]);
            }
            $data_raw = $data_raw->select();
        }
        $data = ["data_list"=>[],"count"=>0];
        foreach ($data_raw as $item){
            array_push($data["data_list"],$item->data);
        }
        $data["count"] = $this->count();
        return $data;
    }
}
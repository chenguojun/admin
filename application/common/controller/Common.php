<?php
namespace app\common\controller;
use \think\View;
use \think\Controller;
use \think\Exception;
use \think\Model;
use \think\Validate;
class Common extends Controller{
    public $item_list = [];
    public $crumb_list = [];
    public $table_name = "";
    public $field = [];
    public $action = [];
    public $map_key = "";
    public function add_field($name,$description,$kind,$optional=[]){
        $item = [];
        $item["name_e"]=$name;
        $item["name_c"]=$description;
        $item["kind"]=$kind;
        if($optional){
            $item["optional"]=$optional;
        }
        array_push($this->item_list,$item);
        array_push($this->field,$name);
        return $this;
    }
    public function add(){
//        if(empty($this->table_name)){
//            throw new Exception('表名不能为空', 100001);
//        }
        if(request()->isPost()){
            $validate_rule = [];
            foreach ($this->item_list as $item){
                if(!empty($item["optional"]["validate"])){
                    $validate_rule[$item["name_e"]]=$item["optional"]["validate"];
                }
            }
            $validate = new Validate($validate_rule);
            if (!$validate->check(input(''))) {
                dump($validate->getError());
            }
            $common_model = model("Common");
            $common_model->setTable($this->table_name);
            $common_model->allowField(true)->save(input(""));
            exit();
        }else{
            $this->assign("list",$this->item_list);
            echo $this->fetch("common@/add");
        }
    }
    public function edit(){
//        if(empty($this->table_name)){
//            throw new Exception('表名不能为空', 100001);
//        }
        if(request()->isPost()){
            $validate_rule = [];
            foreach ($this->item_list as $item){
                if(!empty($item["optional"]["Validate"])){
                    $validate_rule[$item["name_e"]]=$item["optional"]["validate"];
                }
            }
            $validate = new Validate($validate_rule);
            if (!$validate->check(input(''))) {
                dump($validate->getError());
            }
            $common_model = model("Common");
            $common_model->setTable($this->table_name);
            $common_model->allowField(true)->save(input(""),[$this->map_key=>input($this->map_key)]);
            $this->success("修改成功");
            exit();
        }else{
            $common_model = model("Common");
            $common_model->setTable($this->table_name);
            $data = $common_model->where($this->map_key,input($this->map_key))->find();
            $this->assign("data",$data);
            $this->assign("list",$this->item_list);
            $this->assign("map_key",$this->map_key);
            echo $this->fetch("common@/edit");
        }
    }
    public function table_name($name){
        $this->table_name=$name;
        return $this;
    }
    public function common_list(){
        $this->assign("list",$this->item_list);
        $this->assign("crumb_list",$this->crumb_list);
        $common_model = model("Common");
        $common_model->setTable($this->table_name);
        $data = $common_model->get_true_data();
        $this->assign("data",$data["data_list"]);
        $this->assign("count",$data["count"]);
        $this->assign("action",$this->action);
        $this->assign("map_key",$this->map_key);
        echo $this->fetch("common@/list");
    }
    public function add_Crumb($name,$url=""){
        $crumb["name"]=$name;
        $crumb["url"]=$url;
        array_push($this->crumb_list,$crumb);
        return $this;
    }
    public function set_edit_url($url,$text){
        $this->action["edit"]["url"] = $url;
        $this->action["edit"]["text"] = $text;
        return $this;
    }
    public function set_add_url($url,$text){
        $this->action["add"]["url"] = $url;
        $this->action["add"]["text"] = $text;
        return $this;
    }
    public function set_map($map_key){
        $this->map_key=$map_key;
        return $this;
    }
}


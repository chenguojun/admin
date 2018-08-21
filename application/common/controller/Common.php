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
    public $search = [];
    public $map_key = "";
    public $buttons = ["delete"=>true,"add"=>true,"edit"=>true];
    public $notsort = "[0]";
    public $a_name = "新建内容";
    public function set_search($kind=null,$time=null,$name=null){
        $this->search["kind"]=$kind;
        $this->search["time"]=$time;
        $this->search["name"]=$name;
        return $this;
    }
    public function set_buttons($add=true,$edit=true,$delete=true){
        $this->buttons["add"]=$add;
        $this->buttons["edit"]=$edit;
        $this->buttons["delete"]=$delete;
        return $this;
    }
    public function set_name($title){
        $this->a_name=$title;
        return $this;
    }
    public function set_notsort($data){
        $this->notsort=$data;
        return $this;
    }
    public function add_field($name,$description,$kind,$optional=[],$op=[]){
        $item = [];
        $item["name_e"]=$name;
        $item["name_c"]=$description;
        $item["kind"]=$kind;
        if($optional){
            $item["optional"]=$optional;
        }
        if($op){
            $item["op"]=$op;
        }
        array_push($this->item_list,$item);
        array_push($this->field,$name);
        return $this;
    }
    public function add(){
        if(!$this->buttons["add"]) $this->error("此功能未启用",url('ok'));
        if(request()->isPost()){
            $data = input("");
            $validate_rule = ["__token__"=>"require|token"];
            $field = [];
            foreach ($this->item_list as &$item){
                if(!empty($item["optional"]["validate"])){
                    $validate_rule[$item["name_e"]]=$item["optional"]["validate"];
                    $field[$item["name_e"]]=$item["name_c"];
                }
                if(!empty($item["optional"]["function"])){
                    $data[$item["name_e"]]=call_user_func($item["optional"]["function"],$data[$item["name_e"]]);
                }
                if($item["kind"]=="checkbox"){
                    $data[$item["name_e"]] = input($item["name_e"]) == "on" ? 1 : 0;
                }
            }
            $validate = new Validate($validate_rule,[],$field);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $common_model = model("Common");
            $common_model->setTable($this->table_name);
            $common_model->allowField(true)->save($data);
            system_log("新建内容(id:".$common_model->id .",模块:".$this->a_name.")","内容操作");
            $this->success("添加成功",url('ok'));
            exit();
        }else{
            $new_list = [];
            $count = 0;
            foreach ($this->item_list as $vo){
                if(empty($vo["op"])){
                    array_push($new_list,$vo);
                }else{
                    foreach ($vo["op"] as $vo1){
                        if($vo1 == "add") array_push($new_list,$vo);

                    }
                }
                if($vo["kind"] == "file" || $vo["kind"] == "image" || $vo["kind"] == "images") $count++;
            }
            $this->assign("list",$new_list);
            $this->assign("count",$count);
            echo $this->fetch("common@/add");
        }
    }
    public function edit(){
        if(!$this->buttons["edit"]) $this->error("此功能未启用",url('ok'));
        if(request()->isPost()){
            $data = input("");
            $validate_rule = ["__token__"=>"require|token"];
            $field = [];
            foreach ($this->item_list as $item){
                if(!empty($item["optional"]["validate"])){
                    $validate_rule[$item["name_e"]]=$item["optional"]["validate"];
                    $field[$item["name_e"]]=$item["name_c"];
                }
                if(!empty($item["optional"]["function"])){
                    $data[$item["name_e"]]=call_user_func($item["optional"]["function"],$data[$item["name_e"]]);
                }
                if($item["kind"]=="checkbox"){
                    $data[$item["name_e"]] = input($item["name_e"]) == "on" ? 1 : 0;
                }
            }
            $validate = new Validate($validate_rule,[],$field);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $common_model = model("Common");
            $common_model->setTable($this->table_name);
            $common_model->allowField(true)->save($data,[$this->map_key=>input($this->map_key)]);
            system_log("编辑内容(id:".$common_model->id .",模块:".$this->a_name.")","内容操作");
            $this->success("修改成功",url('ok'));
            exit();
        }else{
            $common_model = model("Common");
            $common_model->setTable($this->table_name);
            $data = $common_model->where($this->map_key,input($this->map_key))->find();
            $this->assign("data",$data);
            $new_list = [];
            $count = 0;
            foreach ($this->item_list as $vo){
                if(empty($vo["op"])){
                    array_push($new_list,$vo);
                }else{
                    foreach ($vo["op"] as $vo1){
                        if($vo1 == "edit") array_push($new_list,$vo);
                    }
                }
                if($vo["kind"] == "file" || $vo["kind"] == "image" || $vo["kind"] == "images") $count++;
            }
            $this->assign("list",$new_list);
            $this->assign("count",$count);
            $this->assign("map_key",$this->map_key);
            echo $this->fetch("common@/edit");
        }
    }
    public function table_name($name){
        $this->table_name=$name;
        return $this;
    }
    public function common_list(){
        $new_list = [];
        foreach ($this->item_list as $vo){
            if(empty($vo["op"])){
                array_push($new_list,$vo);
            }else{
                foreach ($vo["op"] as $vo1){
                    if($vo1 == "view") array_push($new_list,$vo);
                }
            }
        }
        $this->assign("list",$new_list);
        $this->assign("crumb_list",$this->crumb_list);
        $common_model = model("Common");
        $common_model->setTable($this->table_name);
        if (input("?get.search_kind") || input("?get.search_name") || input("?get.search_time_max") || input("?get.search_time_min")){
            $data = $common_model->get_true_data($this->search,input("get."));
            $this->assign("search_data",input("get."));
        }else{
            $data = $common_model->get_true_data();
        }
        $this->assign("data",$data["data_list"]);
        $this->assign("count",$data["count"]);
        $this->assign("map_key",$this->map_key);
        $this->assign("search",$this->search);
        $this->assign("notsort",$this->notsort);
        $this->assign("buttons",$this->buttons);
        $this->assign("name",$this->a_name);
        echo $this->fetch("common@/list");
    }
    public function add_Crumb($name,$url=""){
        $crumb["name"]=$name;
        $crumb["url"]=$url;
        array_push($this->crumb_list,$crumb);
        return $this;
    }
    public function set_map($map_key){
        $this->map_key=$map_key;
        return $this;
    }
    public function delete(){
        if(!$this->buttons["delete"]) $this->error("此功能未启用",url('ok'));
        $common_model = model("Common");
        $common_model->setTable($this->table_name);
        foreach (input($this->map_key."/a") as $item){
            system_log("删除内容(id:".$item .",模块:".$this->a_name.")","内容操作");
            $common_model->where($this->map_key,$item)->delete();
        }
        echo json_encode(["status"=>"1"]);
    }
    public function common(){
        $op = input("op");
        switch ($op){
            case "":
                $this->common_list();
                break;
            case "add":
                $this->add();
                break;
            case "edit":
                $this->edit();
                break;
            case "delete":
                $this->delete();
                break;
            default:
                $this->common_list();
        }
    }
}


<?php
/**
 * 生成随机字符串
 * @param $length int 字符串长度
 * @return $nonce string 随机字符串
 */
function get_randstr($length=32) {
    $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $nonce = '';
    for ($i=0; $i<$length; $i++) {
        $nonce .= $str[mt_rand(0, 61)];
    }
    return $nonce;
}

/**
 * @param $time
 * @return false|string
 * 格式化时间
 */
function time_format($time){
    return date("Y-m-d H:i:s",$time);
}

function get_role_name($role_id){
    return db("role")->where("id",$role_id)->select()[0]["role_name"];
}

/**
 * @return string
 * 返回当前模块.控制器.操作
 */
function get_now_action(){
    if(input("op")=="edit" || input("op")=="add" || input("op")=="delete"){
        return request()->module().".".request()->controller().".".request()->action()."_".input("op");
    }else{
        return request()->module().".".request()->controller().".".request()->action();
    }
}

function system_log($action,$kind){
    $user = model("User");
    $data["type"]=$kind;
    $data["content"]=$action;
    $data["username"]=$user->get_user_info()["username"];
    $data["ip"]=request()->ip();
    db("system_log")->insert($data);
}

/**
 * @param $time
 * @return false|string
 * 格式化日期
 */
function date_now($time=null){
    if(empty($time)){
        return date("Y-m-d",time());
    }else{
        return date("Y-m-d",$time);
    }
}

/**
 * @param $pat 配置前缀
 * @param $rep 数据变量
 * @return bool 返回状态
 */
function set_db_config($pat, $rep){
    if (is_array($pat) and is_array($rep)) {
        for ($i = 0; $i < count($pat); $i++) {
            $pats[$i] = '/\'' . $pat[$i] . '\'(.*?),/';
            $reps[$i] = "'". $pat[$i]. "'". "=>" . "'".$rep[$i] ."',";
        }
        $fileurl = APP_PATH . "database.php";
        $string = file_get_contents($fileurl); //加载配置文件
        $string = preg_replace($pats, $reps, $string); // 正则查找然后替换
        file_put_contents($fileurl, $string); // 写入配置文件
        return true;
    } else {
        return false;
    }
}
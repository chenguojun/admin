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
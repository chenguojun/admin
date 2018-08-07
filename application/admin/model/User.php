<?php
namespace app\admin\model;

use think\Model;
use think\db;
class User extends Model{
    protected $_validate = array(
        array('username', 'require', '用户名不能为空', 1, 'regex', 3),
        array('username', '', '用户已存在', 2, 'unique', 1),
        array('password', 'require', '密码不能为空', 1, 'regex', 1),
        array('confirm_password', 'password', '确认密码不正确', 0, 'confirm', 1),
        array('email', 'require', '邮箱不能为空', 1, 'regex', 3),
        array('email', 'email', '邮箱格式不正确', 2, 'regex', 3),
        array('email', '', '邮箱已被占用', 2, 'unique', 1)
    );

    protected $_auto = array(
        array('password', 'md5', 3, 'function'),
        array('nickname', 'username', 1, 'field'),
        array('register_time', 'time', 1, 'function')
    );

    /**
     * 检测用户是否存在
     */
    public function is_user_exists($username) {
        if (empty($username)) {
            return false;
        }
        $map['username'] = $username;
        $user = Model('User')->where($map)->find();
        if (empty($user)) {
            return false;
        }
        return true;
    }

    /**
     * 验证登录
     */
    public function check_login($password, $username = '') {
        if (!$username) {
            $username = input('username');
        }
        $user = $this->get_user($username, $password);
        if ($user === false) {
            return false;
        }
        $this->update_user($username, $password);
        return true;
    }

    /**
     * 更新用户信息
     */
    public  function update_user($username, $password){
        if (empty($username) || empty($password)) {
            return false;
        }
        $map['username'] = $username;
        $map['password'] = md5($password);
        $user = Model('User')->where($map)->update(["last_login_time"=>time(),"last_login_ip"=>request()->ip()]);
    }

    /**
     * 更新auth_code
     */
    public function refresh_auth_token($username, $password){
        if (empty($username) || empty($password)) {
            return false;
        }
        $map['username'] = $username;
        $map['password'] = md5($password);
        $auth_token = get_randstr(50);
        $user = Model('User')->where($map)->update(["auth_token"=>$auth_token,"auth_token_time"=>time()]);
        return $auth_token;
    }


    /**
     * 获取用户信息
     */
    public function get_user($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }
        $map['username'] = $username;
        $map['password'] = md5($password);
        $user = Model('user')->where($map)->find();
        if (empty($user)) {
            return false;
        }
        return $user;
    }

    public function get_user_info(){
        $auth_token = session("auth_token");
        $map['auth_token'] = $auth_token;
        $user = Model('user')->setTable("user")->where($map)->select()[0];
        return $user;
    }

    public function logout(){
        $auth_token = session("auth_token");
        $map['auth_token'] = "";
        $map['auth_token_time'] = "";
        Model('user')->where("auth_token",$auth_token)->update($map);
    }


    public function change_password($new_pwd) {
        $auth_token = session("auth_token");
        if (empty($user) || empty($new_pwd)) {
            return false;
        }
        $map['auth_token'] = $auth_token;
        Model('User')->where($map)->setField('password', md5($new_pwd));
        return true;
    }

    public function check_auth_token($auth_token){
        if (empty($auth_token)) {
            return false;
        }
        $map['auth_token'] = $auth_token;
        $user = Model('user')->where($map)->find();
        if (empty($user)) {
            return false;
        }
        if (time()-$user["auth_token_time"] > 7200){
            return false;
        }
        return true;
    }
    public function check_auth($action,$auth_token){
        if (empty($auth_token)) {
            return false;
        }
        $map['auth_token'] = $auth_token;
        $user = Model('User')->where($map)->find();
        if (empty($user)) {
            return false;
        }
        if (time()-$user["auth_token_time"] > 7200){
            return false;
        }
        if(!strpos($this->get_role_value($user["role"]),$action) && $this->get_role_value($user["role"]) != '0'){
            return false;
        }
        return true;
    }
    public function get_role_value($id){
        return Model("User")->setTable("role")->where(["id"=>$id])->find()["value"];
    }
    public function role_delete($id){
        return Model("User")->setTable("role")->where(["id"=>$id])->delete();
    }
    public function get_role_rule(){
        return Model("User")->setTable("role")->select();
    }
}
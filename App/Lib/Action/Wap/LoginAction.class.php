<?php

//登陆
class LoginAction extends LoadAction {

    public function index() {
        $web_id = $this->website['web_id'];//echo($web_id);
        $this->assign("web_id",$web_id);
        $is_weixin = $this->is_weixin();
        $this->assign("is_weixin",$is_weixin);
        $this->display();
    }

    public function register() {
        $web_id = $this->website['web_id'];//echo($web_id);获取网站id
        $this->assign("web_id",$web_id);
        $this->display();
    }

    public function login() {
        if ($this->isPost()) {
            if (empty($_POST['username']) || empty($_POST['userpass'])) {
                echo "账户或密码不可为空";
                exit();
            } else {
                echo $this->logins($_POST['username'], $_POST['userpass']);
            }
        }
    }

    //注册
    public function registers() {
        if ($this->isPost()) {
            $isok = $this->btregister(trim($_POST[username]), trim($_POST[penname]), '无', trim($_POST[userpass]));
            if ($isok == 1) {
                echo $this->logins($_POST[username], $_POST[userpass]);
            } else {
                echo $isok;
            }
        }
    }

    //用户登录
    public function logins($user_name, $user_pass) {
        $User = M('User');
        $where['user_name'] = $user_name;
        $where['user_pass'] = md5($user_pass . $this->website[all_ps]);
        $where['web_id'] = $this->website[web_id];
        $isUser = $User->field('`user_id`,`web_id`,`agent_id`,`user_name`,`pen_name`,`user_pass`,`portrait`,`alance`')->where($where)->find();
        if ($isUser) {
            cookie('user_id', $isUser[user_id], time() + 2 * 7 * 24 * 3600);
            cookie('user_name', $isUser[user_name], time() + 2 * 7 * 24 * 3600);
            cookie('pen_name', $isUser[pen_name], time() + 2 * 7 * 24 * 3600);
            cookie('portrait', $isUser[portrait], time() + 2 * 7 * 24 * 3600);
            cookie('shell', md5($isUser[user_name] . $isUser[user_pass] . $this->website[all_ps]), time() + 2 * 7 * 24 * 3600);
            //更新等级
            $data['login_ip'] = get_client_ip();
            $data['login_time'] = date("Y-m-d H:i:s");
            $User->where(array('user_id'=>$isUser[user_id]))->save($data);
            return 1;
        } else {
            return "账户密码错误！";
        }
    }

    //用户注册
    public function btregister($user_name, $pen_name, $email, $password) {
        $user = M('User');
        $agent = M('Agent');
        //验证账户
        if (!empty($user_name)) {
            $where['web_id'] = $this->website[web_id];
            $where['user_name'] = $user_name;
            $count = $user->where($where)->sum('user_id');
            if ($count > 0) {
                return "账户已存在请重新注册";
            }
        } else {
            return "账户不得为空！";
        }
        if (empty($pen_name)) {
            return "昵称不得为空！";
        }
        //判断是否存在推广id
        if ($_COOKIE[agent]) {
            $prid = $agent->where(array('agent_id' => $_COOKIE[agent]))->sum('agent_id');
            if ($prid > 0) {
                $data['agent_id'] = $_COOKIE[agent];
            } else {
                $data['agent_id'] = $this->website[master_id];
            }
        } else {
            $data['agent_id'] = $this->website[master_id];
        }
        //获取注册的推广ID
        $password = md5($password . $this->website[all_ps]);
        $data['web_id'] = $this->website[web_id];
        $data['uid'] = 2; //读者
        $data['user_name'] = $user_name;
        $data['pen_name'] = $pen_name;
        $data['user_pass'] = $password;
        $data['email'] = $email;
        $data['login_ip'] = get_client_ip();
        $data['login_time'] = date('y-m-d H:i:s', time());
        $data['vip_time'] = $data['login_time'];
        $data['sign_time'] = date('Y-m-d', strtotime($data['login_time'] . "-1 day"));
        $data['registration_time'] = $data['login_time'];
        $uid = $user->add($data);
        if ($_COOKIE['channel']) {
            $tongji['reg_total'] = array('exp', "reg_total+1");
            $agent->where(array('channel_id' => $_COOKIE['channel']))->save($tongji);
        }
        //增加统计
        if ($uid > 0) {
            return 1; //OK
        } else {
            return "系统错误"; //NO
        }
    }

    //修改密码
    public function pass() {
        if ($this->isPost()) {
            $user = M('User');
            $where['user_id'] = $_COOKIE['user_id'];
            $myuser = $user->where($where)->field('user_pass')->find();
            $yuan = md5($_POST[oldpassword] . $this->website[all_ps]);
            if ($yuan == $myuser[user_pass]) {
                $pass = md5($_POST[password] . $this->website[all_ps]);
                $data['user_pass'] = $pass;
                $user->where($where)->save($data);
                cookie('user_id', null);
                cookie('user_name', null);
                cookie('pen_name', null);
                cookie('portrait', null);
                cookie('shell', null);
                echo 1;
            } else {
                echo "对不起修改密码没有成功请在输入您原先的密码！！";
            }
        }
    }

    //退出
    public function logout() {
        cookie('user_id', null);
        cookie('user_name', null);
        cookie('pen_name', null);
        cookie('portrait', null);
        cookie('shell', null);
        header("Location: /");
    }

}

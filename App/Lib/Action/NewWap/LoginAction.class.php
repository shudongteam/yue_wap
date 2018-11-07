<?php

//登陆
class LoginAction extends LoadingAction {

    public function index() {
        $redirect_url = I('redirect_url');
        $web_id = $this->website['web_id'];
        $this->assign("web_id",$web_id);
        $is_weixin = $this->is_weixin();
        $this->assign("is_weixin",$is_weixin);
        $this->assign("redirect_url",$redirect_url);
        $this->display();
    }

    public function register() {
        if ($this->isPost()) {
            $user_name = trim($_POST[user_name]);
            $user_pass = trim($_POST[user_pass]);      
            echo $this->btregister($user_name, $user_pass);
        } else {
            $this->display();     
        }
    }

    public function login() {
        if ($this->isPost()) {
            if (empty($_POST['user_name']) || empty($_POST['user_pass'])) {
                echo "账户或密码不可为空";
                exit();
            } else {
                echo $this->logins($_POST['user_name'], $_POST['user_pass']);
            }
        }
    }


    //用户登录
    public function logins($user_name, $user_pass) {
        $User = D('NUserView');
        $where['user_name'] = $user_name;
        $where['user_pass'] = md5(C('All_ps').$user_pass);
        $where['web_id'] = $this->website[web_id];
        $isUser = $User->where($where)->find();
        if ($isUser) {
            cookie('user_id', $isUser['user_id'], time() + 2 * 7 * 24 * 3600);
            cookie('pen_name', $isUser[pen_name], time() + 2 * 7 * 24 * 3600);
            cookie('portrait', $isUser[portrait], time() + 2 * 7 * 24 * 3600);
            cookie('shell', md5($user_name . $where['user_pass'] . C('ALL_ps')), time() + 2 * 7 * 24 * 3600);       
            return 1;
        } else {
            return "账户密码错误！";
        }
    }

    //用户注册
    public function btregister($user_name, $password) {
        //验证账户
        if (!empty($user_name)) {
            $where['web_id'] = $this->website[web_id];
            $where['user_name'] = $user_name;
            $user = M('NUser');
            $count = $user->where($where)->sum('user_id');
            if ($count > 0) {
                return "账户已存在请重新注册";
            }
        } else {
            return "账户不得为空！";
        }

        //判断是否存在推广id
        if ($_COOKIE[agent]) {
            $agent = M('NAgent');
            $prid = $agent->where(array('agent_id' => $_COOKIE[agent]))->find();
            if ($prid) {
                $data['agent_id'] = $_COOKIE[agent];
            } else {
                $data['agent_id'] = $this->website[master_id];
            }
        } else {
            $data['agent_id'] = $this->website[master_id];
        }

        //判断是否存在渠道id
        if ($_COOKIE['channel']) {
            $agentChannel = M('NAgentChannel');
            $tongji['reg_total'] = array('exp', "reg_total+1");
            $agentChannel->where(array('channel_id' => $_COOKIE['channel']))->save($tongji);
        }

        $data['web_id'] = $this->website[web_id];
        $data['user_name'] = $user_name;
        $data['user_pass'] = md5(C('All_ps').$password);
        $data['pen_name'] =  'dz'. date('YmdHis', time());
        $data['portrait'] = 'portrait';
        $data['sex'] = 3;
        $data['time'] = date('y-m-d H:i:s', time());
        $uid = $user->add($data);
        if (!$uid) {
            exit("系统错误");
        }
        $data['user_id'] = $uid;
        $userInfo = M('NUserInfo');        
        $info = $userInfo->add($data);
        $userMoney = M('NUserMoney');
        $data['vip_time'] = $data['time'];
        $money = $userMoney->add($data);
        if ($info && $money) {
            cookie('user_id', $data['user_id'], time() + 2 * 7 * 24 * 3600);
            cookie('pen_name', $data['pen_name'], time() + 2 * 7 * 24 * 3600);
            cookie('portrait', $data['portrait'], time() + 2 * 7 * 24 * 3600);
            cookie('shell', md5($user_name . $data['user_pass']. C('ALL_ps')), time() + 2 * 7 * 24 * 3600); 
            return 1;
        } else {
            exit("系统错误2");
        }
    }

    //修改密码
    public function pass() {
        // if ($this->isPost()) {
        //     $user = M('User');
        //     $where['user_id'] = $_COOKIE['user_id'];
        //     $myuser = $user->where($where)->field('user_pass')->find();
        //     $yuan = md5($_POST[oldpassword] . $this->website[all_ps]);
        //     if ($yuan == $myuser[user_pass]) {
        //         $pass = md5($_POST[password] . $this->website[all_ps]);
        //         $data['user_pass'] = $pass;
        //         $user->where($where)->save($data);
        //         cookie('user_id', null);
        //         cookie('user_name', null);
        //         cookie('pen_name', null);
        //         cookie('portrait', null);
        //         cookie('shell', null);
        //         echo 1;
        //     } else {
        //         echo "对不起修改密码没有成功请在输入您原先的密码！！";
        //     }
        // }
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

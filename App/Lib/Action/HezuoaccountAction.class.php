<?php

//第三方合作登录
class HezuoaccountAction extends Action {

    public function login($openid) {
        //准备工作
        $User = M('User');
        $where[openid] = $openid;
        $where['web_id'] = $this->website['web_id'];  
        $isUser = $User->field('`user_id`,`web_id`,`user_name`,`pen_name`,`user_pass`,`portrait`,`alance`')->where($where)->find();
        
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
            if ($_GET[backUrl]) {
                header("Location:$_GET[backUrl]");
            } else {
                header("Location: /Index/index.html");
            }
            exit();
        } else {
            return 2; //失败
        }
    }

    //注册账户
    public function registers($openid, $type) {
        $user = M('User');
        $agent = M('AgentChannel');
        //判断是否存在推广id
        if ($_COOKIE['channel']) {
            $result = $agent->where(array('channel_id' => $_COOKIE['channel']))->find();
            if (is_array($result)) {
                $data['agent_id'] = $result['agent_id'];
            } else {
                $data['agent_id'] = $this->website[master_id];
            }
        } else {
            $data['agent_id'] = $this->website[master_id];
        }
        //获取注册的推广ID
        $password = md5("xg123456" . $this->website[all_ps]);
        $data['web_id'] = $this->website['web_id'];
        $data['uid'] = 2;
        $data['user_name'] = "dz" . date('YmdHis', time()) . $this->getRandChar(3);
        $data['pen_name'] = $type . "读者" . $this->getRandChar(6);
        $data['user_pass'] = $password;
        $data['openid'] = $openid; //绑定唯一ID
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
        if ($uid > 0) {
            $this->login($openid);
            exit();
        } else {
            echo "系统错误";
        }
    }

    //生成随机码
    function getRandChar($length) {
        $str = null;
        $strPol = "0123456789";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str.=$strPol[rand(0, $max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }

}

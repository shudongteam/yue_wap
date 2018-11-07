<?php

//第三方账户
class AccountsAction extends LoadingAction {

    //扣扣登录
    public function qq() {
        vendor("Oauth.Oauth");
        $oauth = new Oauth($this->website, 'qq');
        $oauth->login();
    }

    //微博登录
    public function weibo() {
        vendor("Oauth.Oauth");
        $oauth = new Oauth($this->website, 'wb');
        $oauth->encode = false;        
        $oauth->login();
    }


    //微信登录
    public function weixin() {
        //http://weimeng.ymzww.cn/chapter/13224/5.html?agent=28&channel=185&focus=10
        $redirect_uri = urlencode('http://' . $this->website['login_url'] . '/Accounts/wxcallback.html?backUrl=') . base64_encode($_GET[backUrl]);
        //授权弹框scope=snsapi_userinfo
        //静默授权
        //$url='https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->website['wx_id'] . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_base&state=' . $this->website['web_url'] . '&connect_redirect=1#wechat_redirect';
        //echo $url;
        header('location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->website['wx_id'] . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_base&state=' . $this->website['web_url'] . '&connect_redirect=1#wechat_redirect');
    }


function test (){
    $access = $this->get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxbbd3b017b3e18d83&secret=89c98fc18b2779a1313ef1c710c5e3df');
    $access = json_decode($access, true);
    $openid = 'oS7941bpPU-6wGr_sOvjkBr73lEU';
    if (isset($access['access_token'])) {
        $user_data = $this->get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access['access_token'].'&openid='.$openid);
        $user_data = json_decode($user_data, true);
        var_dump($user_data);
    }
}

    //登录并注册
    public function login($openid, $login_type = '') {
            header("Content-type:text/html;charset=utf-8");
         //echo $openid,$login_type,$_GET[backUrl];
      //  exit;
        // echo $_GET[backUrl];exit;
        $User = D('NUserView');
        $where['openid'] = $openid;
        $where['web_id'] = $this->website[web_id];
        $data = $User->where($where)->find();
        if (!$data){
            $user = M('NUser');
            $userInfo = M('NUserInfo');
            $agent = M('NAgentChannel');
            //判断是否存在推广id
            if ($_COOKIE['channel']) {
                $result = $agent->where(array('channel_id' => $_COOKIE['channel']))->find();
                if (is_array($result)) {
                    //渠道链接注册人数+1
                    $tongji['reg_total'] = array('exp', "reg_total+1");
                    $agent->where(array('channel_id' => $_COOKIE['channel']))->save($tongji);
                    //推广人的agent_id
                    $data['agent_id'] = $result['agent_id'];
                } else {
                    //一级代理ID
                    $data['agent_id'] = $this->website[master_id];
                }
            } else {
                $data['agent_id'] = $this->website[master_id];
            }
            $data['web_id'] = $this->website['web_id'];
            $data['user_name'] = $login_type . date('YmdHis', time()) . $this->getRandChar(4);
            $data['user_pass'] = md5(C('All_ps') . "abc123456");
            $data['openid'] = $openid;
            $data['time'] = date('y-m-d H:i:s', time());
            $nickname = $login_type . date('YmdHis', time());
            $sex = 3;
            $portrait = 'portrait';
            if ($login_type == '微信') {
                // $access = $this->get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->website['wx_id'].'&secret='.$this->website['wx_secret']);
                // $access = json_decode($access, true);
                // if (isset($access['access_token'])) {
                    // $user_data = $this->get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access['access_token'].'&openid='.$openid);
                    $user_data = $this->get_contents('http://m.kyueyun.com/Wx/get_user_info/openid/'.$openid);
                    $user_data = json_decode($user_data, true);
                    //var_dump($user_data);exit();
                    if (!isset($user_data['subscribe'])) {
                        exit('获取微信个人资料失败!');
                    }

                    if ($user_data['subscribe']) {
                        //已关注
                        $nickname = $user_data['nickname'];
                        $sex = $user_data['sex'];
                        $portrait = $user_data['headimgurl'];
                    } else {
                        # 未关注的情况下
                        //$nickname = $login_type."用户".time();
                    }

                    // if (!isset($user_data['nickname'])) {
                    //     exit('获取微信个人资料失败!');
                    // }

                // } else {
                //     exit('获取token失败!');
                // }
            } //elseif ($login_type == 'QQ') {
              //  $url = 'https://graph.qq.com/user/get_user_info?access_token='.$token.'&oauth_consumer_key='.$this->website[qq_id].'&openid='.$openid;
              ///  $user_data = $this->get_contents($url);
              //  $user_data = json_decode($user_data, true);
               // if (!isset($user_data['nickname'])) {
               //     exit('获取QQ个人资料失败!');
              //  }
              //  $nickname = $user_data['nickname'];
              ////  if ($user_data['gender'] == '男') {
               //     $sex = 1;
              //  } elseif ($user_data['gender'] == '女') {
               //     $sex = 2;
              //  } else {
               //     $sex = 3;
             //   }
              //  $portrait = $user_data['figureurl_qq_2'] ? $user_data['figureurl_qq_2'] : $user_data['figureurl_qq_1'];
          //  } else {
                //微博
              //  $url = 'https://api.weibo.com/2/users/show.json?access_token='.$token.'&uid='.$openid;
              //  $user_data = $this->get_contents($url);
              //  $user_data = json_decode($user_data, true);
             //   if (!isset($user_data['screen_name'])) {
              //      exit('获取微博个人资料失败!');
             //   }
              //  $nickname = $user_data['screen_name'];
              //  if ($user_data['gender'] == 'm') {
              //      $sex = 1;
              //  } elseif ($user_data['gender'] == 'f') {
               //     $sex = 2;
             //   } else {
              //      $sex = 3;
             //   }
             //   $portrait = $user_data['profile_image_url'];
           // }
            $data['pen_name'] = $nickname;
            $data['sex'] = $sex;
            $data['portrait'] = $portrait;

            $uid = $user->add($data);
            if (!$uid) {
                exit("系统错误");
            }
            $data['user_id'] = $uid;
            $info = $userInfo->add($data);
            $userMoney = M('NUserMoney');
            $data['vip_time'] = $data['time'];
            $money = $userMoney->add($data);
            if (!$info || !$money) {
                exit('系统错误');
            }            
        }
        // var_dump($data);exit;
        cookie('user_id', $data['user_id'], time() + 2 * 7 * 24 * 3600);
        cookie('pen_name', $data['pen_name'], time() + 2 * 7 * 24 * 3600);
        cookie('portrait', $data['portrait'], time() + 2 * 7 * 24 * 3600);
        cookie('shell', md5($data['user_name'] . $data['user_pass'] . C('All_ps')), time() + 2 * 7 * 24 * 3600);
        if ($_GET[backUrl]) {
            header("Location:$_GET[backUrl]");
        } else {
            header("Location: /Index/index.html");
        }    
    }

    //生成随机码
    private function getRandChar($length) {
        $str = null;
        $strPol = "0123456789";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str.=$strPol[rand(0, $max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }


    private function get_contents($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }    
}

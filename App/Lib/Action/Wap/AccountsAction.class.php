<?php

//第三方账户
class AccountsAction extends LoadingAction {

    //扣扣登录
    public function qq() {
        vendor("Oauth.Oauth");
        $oauth = new Oauth($this->website, 'qq');
        $oauth->login();
    }

    //扣扣返馈
    public function qqcallback() {
        vendor("Oauth.Oauth");
        $oauth = new Oauth($this->website, 'qq');
        $openid = $oauth->callback();
        $token = $oauth->access_token;
        $login_type = 'QQ';
        $backUrl = isset($_GET[backUrl]) ? base64_decode($_GET[backUrl]) : $this->website[web_url];
        header('location:http://' . $_GET[state] . '/Accounts/login/openid/' . $openid . '/login_type/' . $login_type . '/token/' . $token . '?backUrl=' . $backUrl);
    }

    //微博登录
    public function weibo() {
        vendor("Oauth.Oauth");
        $oauth = new Oauth($this->website, 'wb');
        $oauth->login();
    }

    //微博反馈
    public function wbcallback() {
        vendor("Oauth.Oauth");
        $oauth = new Oauth($this->website, 'wb');
        $oauth->method = 'POST';
        $oauth->encode = false;
        $openid = $oauth->callback();
        $token = $oauth->access_token;
        $login_type = '微博';
        $backUrl = isset($_GET[backUrl]) ? base64_decode($_GET[backUrl]) : $this->website[web_url];
        header('location:http://' . $_GET[state] . '/Accounts/login/openid/' . $openid . '/login_type/' . $login_type . '/token/' . $token . '?backUrl=' . $backUrl);
    }

    //微信登录
    public function weixin() {
        vendor("Oauth.Oauth");
        //http://weimeng.ymzww.cn/chapter/13224/5.html?agent=28&channel=185&focus=10
        $redirect_uri = urlencode('http://' . $this->website['login_url'] . '/Accounts/wxcallback.html?backUrl=') . base64_encode($_GET[backUrl]);
        //授权弹框
        //header('location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->website['wx_id'] . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_userinfo&state=' . $this->website['web_url'] . '&connect_redirect=1#wechat_redirect');
        
        //静默授权
        header('location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->website['wx_id'] . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_base&state=' . $this->website['web_url'] . '&connect_redirect=1#wechat_redirect');
        // file_put_contents("weixin.txt", $redirect_uri);
    }

    //微信反馈
    public function wxcallback() {
        vendor("Oauth.Oauth");
        $oauth = new Oauth($this->website, 'wx');
        $openid = $oauth->callback();
        $login_type = '微信';
        header('location:http://' . $_GET[state] . '/Accounts/login/openid/' . $openid . '/login_type/' . $login_type . '?backUrl=' . urlencode(base64_decode($_GET[backUrl])));
        // file_put_contents("wxcallback.txt", urlencode(base64_decode($_GET[backUrl])));
    }

    //登录并注册
    public function login($openid, $login_type) {
        $is = A('Hezuoaccount')->login($openid);
        if ($is == 2) {
            A('Hezuoaccount')->registers($openid, $login_type);
        }
    }

}

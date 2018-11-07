<?php

//扣扣，微信，微博登录
class Oauth {

    private $qqurl = 'https://graph.qq.com/oauth2.0/authorize';
    private $qqtoken = 'https://graph.qq.com/oauth2.0/token';
    private $qqopenid = 'https://graph.qq.com/oauth2.0/me';
    private $wburl = 'https://api.weibo.com/oauth2/authorize';
    private $wbtoken = 'https://api.weibo.com/oauth2/access_token';
    private $wbopenid = 'https://api.weibo.com/oauth2/get_token_info';
    public $method = 'GET';
    public $encode = true;
    public $callback_url;
    public $access_token;    

    function __construct($website, $type) {
        $this->website = $website;
        $this->type = $type;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    //登录
    public function login() {
        $state = $this->website['web_url'];
        $key = $this->type . '_id';
        $arr = array(
            "response_type" => "code",
            "client_id" => $this->website[$key],
            "redirect_uri" => $this->callbackurl(),
            "state" => $state,
        );
        $url = $this->combineURL($this->{$this->type . 'url'}, $arr);
        header("Location:$url");
    }

    //反馈
    public function callback() {
        switch ($this->type) {
            case 'qq':
                $token = $this->qqtoken();
                $openid = $this->qqopenid($token);
                break;
            case 'wb':
                $token = $this->wbtoken();
                $openid = $this->wbopenid($token);
                break;
            case 'wx':
                $openid = $this->wxopenid();
                break;
        }

        return $openid;
    }

    //获取微信 openid
    protected function wxopenid() {
        $key = $this->type . '_id';
        $secret = $this->type . '_secret';
        $code = $_GET['code'];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->website[$key] . '&secret=' . $this->website[$secret] . '&code=' . $code . '&grant_type=authorization_code';
        $params = json_decode($this->get_contents($url), true);
        if (isset($params['errcode']) && $params['errcode']) {
            header("Content-type: text/html; charset=utf-8");
            echo '<h1>错误：</h1>' . $params['errcode'];
            echo '<br/><h2>错误信息：</h2>' . $params['errmsg'];
            exit;
        } else {
            return $params['openid'];
        }
    }

    //获取微博 token
    protected function wbtoken() {
        $response = $this->token();
        $params = json_decode($response, true);

        if (isset($params['error']) && $params['error']) {
            header("Content-type: text/html; charset=utf-8");
            echo '<h1>错误：</h1>' . $params['error_code'];
            echo '<br/><h2>错误信息：</h2>' . $params['error'];
            exit;
        } else {
            $this->access_token = $params["access_token"];
            return $params['access_token'];
        }
    }

    //获取qq token
    protected function qqtoken() {
        $response = $this->token();

        if (strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
            $msg = json_decode($response);
            if (isset($msg->error)) {
                header("Content-type: text/html; charset=utf-8");
                echo '<h1>错误：</h1>' . $msg->error;
                echo '<br/><h2>错误信息：</h2>' . $msg->error_description;
                exit;
            }
        }

        $params = array();
        parse_str($response, $params);
        $this->access_token = $params["access_token"];
        return $params["access_token"];
    }

    //获取token
    protected function token() {
        $key = $this->type . '_id';
        $secret = $this->type . '_secret';
        $arr = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->website[$key],
            "redirect_uri" => $this->callbackurl(),
            "client_secret" => $this->website[$secret],
            "code" => $_GET['code']
        );
        $response = $this->get_contents($this->{$this->type . 'token'}, $arr);

        return $response;
    }

    //获取qqopenid
    public function qqopenid($access_token) {
        $arr = array(
            "access_token" => $access_token
        );

        $response = $this->get_contents($this->qqopenid, $arr);
        //--------检测错误是否发生
        if (strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }

        $user = json_decode($response);

        if (isset($user->error)) {
            header("Content-type: text/html; charset=utf-8");
            echo '<h1>错误：</h1>' . $user->error;
            echo '<br/><h2>错误信息：</h2>' . $user->error_description;
            exit;
        }

        return $user->openid;
    }

    //获取微博id
    public function wbopenid($access_token) {
        $arr = array(
            "access_token" => $access_token
        );

        $response = $this->get_contents($this->wbopenid, $arr);
        $params = json_decode($response, true);

        if (isset($params['error']) && $params['error']) {
            header("Content-type: text/html; charset=utf-8");
            echo '<h1>错误：</h1>' . $params['error_code'];
            echo '<br/><h2>错误信息：</h2>' . $params['error'];
            exit;
        } else {
            return $params['uid'];
        }
    }

    //生成反馈链接
    public function callbackurl() {
        if (!isset($this->callback_url) && !$this->encode) {
            return ('http://' . $this->website['login_url'] . '/Accounts/' . $this->type . 'callback.html?backUrl='.$_GET[backUrl]);
        } elseif (!isset($this->callback_url) && $this->encode) {
            return urlencode('http://' . $this->website['login_url'] . '/Accounts/' . $this->type . 'callback.html?backUrl='.$_GET[backUrl]);
        } else {
            return $this->callback_url;
        }
    }

    //拼接url
    public function combineURL($url, $arr) {
        $combined = $url . "?";
        $valuearr = array();

        foreach ($arr as $key => $val) {
            $valuearr[] = "$key=$val";
        }

        $str = implode("&", $valuearr);
        $combined .= ($str);

        return $combined;
    }

    //服务器通过curl请求获得内容
    public function get_contents($url, $arr = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($this->method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arr));
        } elseif ($arr) {
            $url = $this->combineURL($url, $arr);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}

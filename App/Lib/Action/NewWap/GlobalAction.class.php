<?php

//登录界面载入
class GlobalAction extends Action {

    protected $website;

    public function _initialize() {
        //判断是什么站点
       $this->website = $this->website();
        $this->assign('website', $this->website);
        $this->automatic($this->website['automatic']);
        $this->usel_shell($_COOKIE['user_id'], $_COOKIE['shell']);
    }

    //前台是否启动自动登录
    private function automatic($automatic) {
        if (empty($_COOKIE['user_id'])) {
            if ($automatic == 1) {
                $caishu = "?backUrl=". urlencode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                header("Location: /Accounts/weixin.html$caishu");
            } else {
                login();
            }
            exit;
        }
    }

    //判断是否真实登录
    private function usel_shell($user_id, $shell) {
        $User = M('NUser');
        $where['user_id'] = $user_id;
        $isUser = $User->field('`user_name`,`user_pass`')->where($where)->find();
        if ($isUser) {
            $shell2 = md5($isUser['user_name'] . $isUser['user_pass'] . C('ALL_ps'));
            if ($shell != $shell2) {
                login();//调用登录
                exit();
            }
        } else {
            login();//调用登录
            exit();
        }
    }

    //判断是否微信打开链接
    public function is_weixin(){ 
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return 1;
        }  
        return 0;
        }
    //获取网站资料
    private function website() {
        //网站加载
        $isweb = $_SERVER['HTTP_HOST'];
        if (include_once "Website/NewWap/$isweb.php") {
            return $website;
        } else {
            //查找是否存在网站
            preg_match('/wap(\d+)\.{1}/', $isweb, $match);
            if (!$match) {
                $this->error("没有该网站");
            }
            $agent_id = $match[1]; 
             $sql = "SELECT 
                    agent.agent_id master_id, 
                    agent.web_id, agent.pen_name web_name, 
                    agent.mobile webphone, 
                    agent.qq, 
                    agent.automatic,
                    weixin.kefu_qrcode_url weixin
                    FROM `hezuo_n_agent` agent LEFT JOIN `hezuo_n_weixin` weixin 
                    ON agent.agent_id = weixin.agent_id 
                    WHERE agent.agent_id = $agent_id";

            $myweb = M('')->query($sql);
            if ($myweb) {
                $website=array (
                    'web_url' => $isweb,
                    'all_ps' => 'hezuo',
                    'login_url' => 'w.ymzww.cn',
                    'preload' => 'NewWap',
                    'webqq' => '1437940177',
                    'beian' => '苏ICP备14016058号',
                    'qq_id' => '101200146',
                    'qq_secret' => 'e1fca15c14e3a0f1e7b7d5d00c2e4779',
                    'wb_id' => '1175818843',
                    'wb_secret' => 'e78ee3a6a64601ae05b942d3ca96db1f',
                    'wx_id' => 'wx458729fd34adecf1',
                    'wx_secret' => '9654e0ce321ca9bf659f28f17956784e'
              );
                //存在这个网站进行生成
                $file = "Website/NewWap/$isweb.php";
                $text = '<?php $website=' . var_export(array_merge($myweb[0], $website), true) . ';';
                if (false !== fopen($file, 'w+')) {
                    file_put_contents($file, $text);
                    return $website;
                } else {
                    $this->error("生成失败");
                }
            } else {
                $this->error("没有该网站");
            }
        }        
    }
}

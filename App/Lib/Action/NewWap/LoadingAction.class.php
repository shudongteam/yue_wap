<?php
//不加载自动登录功能
class LoadingAction extends Action{

    protected $website;

    public function _initialize() {
        //判断是什么站点
       $this->website = $this->website();
        $this->assign('website', $this->website);
        $this->automatic($this->website['automatic']);
        $this->readname();
    }

    //前台是否启动自动登录
    private function automatic($automatic) {
        if (empty($_COOKIE['user_id'])) {
            if ($automatic == 1) {
                $caishu = "?backUrl=". urlencode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                header("Location: /Accounts/weixin.html$caishu");
            }
        }
    }

    //获取书名章节名
    private function readname(){
         if($_COOKIE[history]){
            $a = str_replace("title"," \"title\" ",$_COOKIE[history]);
            $obj = str_replace("url"," \"url\" ",$a);
            $arr = json_decode($obj,TRUE);
            $arr = explode(":",$arr[title]);
            //$_COOKIE[history] = $arr;
            cookie('lbname', $arr[0], time() + 2 * 7 * 24 * 3600);
            cookie('lcname', $arr[1], time() + 2 * 7 * 24 * 3600);
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
                    'login_url' => 'm.kyueyun.com',
                    'preload' => 'NewWap',
                  //  'webqq' => '1437940177',
                    'beian' => '赣ICP备18001577号-2',
                   // 'qq_id' => '101200146',
                  //  'qq_secret' => 'e1fca15c14e3a0f1e7b7d5d00c2e4779',
                  //  'wb_id' => '1175818843',
                  //  'wb_secret' => 'e78ee3a6a64601ae05b942d3ca96db1f',
                    'wx_id' => 'wxbbd3b017b3e18d83',
                    'wx_secret' => '89c98fc18b2779a1313ef1c710c5e3df'
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

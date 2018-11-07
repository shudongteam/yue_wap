<?php

//登录界面载入
class LoadAction extends Action {

    protected $website;

    public function _initialize() {
        //载入工具包
        $gogju = A('Gongju');
        //判断是什么站点
        $this->website = $gogju->webssite();
        $this->assign('website', $this->website);
        $this->automatic($this->website[automatic]);
    }

    //前台是否启动自动登录
    private function automatic($automatic) {
        if (empty($_COOKIE['user_id'])) {
            $is_weixin = $this->is_weixin();
            if($is_weixin==1){
                if ($automatic == 1) {
                $caishu = "?backUrl=". urlencode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                header("Location: /Accounts/weixin.html$caishu");
                // file_put_contents("d.txt",$_SERVER['REQUEST_URI']);
            }
            }
           
        }
    }
    //判断是否微信打开链接
    public function is_weixin(){ 

        if ( strpos($_SERVER['HTTP_USER_AGENT'], 

        'MicroMessenger') !== false ) {

                return 1;

            }  

                return 0;

        }

}

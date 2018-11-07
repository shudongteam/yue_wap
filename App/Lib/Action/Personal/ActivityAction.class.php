<?php

//活动
class ActivityAction extends Action {

    //年会充值  100元红包
    public function red() {
        if ($_COOKIE['user_id']) {
        	$this->display();
        } else {
            if($this->website[web_id] != 1){
                $back_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                header('location:/Accounts/weixin.html?backUrl=' . $back_url);
                exit;
            }else {
                login(); //调用登录
                exit(); 
            }
        }
    }

}

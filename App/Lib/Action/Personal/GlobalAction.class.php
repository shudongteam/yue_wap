<?php

class GlobalAction extends Action {

    protected $to = 0;
    protected $website;

    public function _initialize() {
        //载入工具包
        $gogju = A('Gongju');
        //判断是什么站点
        $this->website = $gogju->webssite();
        $this->assign('website', $this->website);
        //权限判断
        $this->usel_shell($_COOKIE['user_id'], $_COOKIE['shell']);
        $this->assign("to", $this->to);
    }

    //权限验证页
    private function usel_shell($user_id, $shell) {
        $User = M('User');
        $where['user_id'] = $user_id;
        $isUser = $User->field('`user_id`,`agent_id`,`user_name`,`pen_name`,`user_pass`,`alance`,`integral` ')->where($where)->find();
        if ($isUser) {
            $shell2 = md5($isUser['user_name'] . $isUser['user_pass'] . $this->website[all_ps]);
            if ($shell == $shell2) {
                $this->to = $isUser;
            } else {
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

        if ( strpos($_SERVER['HTTP_USER_AGENT'], 

        'MicroMessenger') !== false ) {

                return 1;

            }  

                return 0;

        }

}

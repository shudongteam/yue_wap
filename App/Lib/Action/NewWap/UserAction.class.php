<?php

//用户信息
class UserAction extends GlobalAction {

    public function info() {
        $user_money = M('NUserMoney')->where(array('user_id' => $_COOKIE['user_id']))->find();
        $money = 0;
        if ($user_money) {
            $money = $user_money['money'];
        }
        //查询用户VIP等级
        $level = A('NewVipLevel')->viplevel($money);
        $this->assign('user_money', $user_money);
        $this->assign('mem_vip',  $level);
        $this->title = '个人中心';
        $this->display();
    }

    public function vip() {
        $this->display();
    }

    public function integral() {
        $this->display();
    }

    

}

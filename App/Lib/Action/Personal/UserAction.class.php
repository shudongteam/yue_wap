<?php

//用户信息
class UserAction extends GlobalAction {

    public function info() {
        $isuser = M('User')->where(array('user_id' => $this->to[user_id]))->field('vote,vipvote,integral,alance')->find();
        $this->assign('isuser', $isuser);
        $this->assign('mem_vip', LevelAction::paylevel($this->to['integral']));
        $this->display();
    }

    public function vip() {
        $this->display();
    }

    public function integral() {
        $this->display();
    }

}

<?php
//个人中心
class IndexAction  extends GlobalAction{
    public function index(){
        $this->assign('mem_vip', LevelAction::paylevel($this->to['integral']));
        $this->assign('web_id', $this->website[web_id]);
        $is_weixin = $this->is_weixin();
        $this->assign("is_weixin",$is_weixin);
        $this->display();
    }
}
